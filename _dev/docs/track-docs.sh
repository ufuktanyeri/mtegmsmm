#!/bin/bash

# SMM Documentation Tracking and Alert System
# This script monitors the SMM documentation files for continuity and provides alerts

set -e

# Configuration
DOCS_DIR="_dev/docs"
MODERNIZATION_PLAN="$DOCS_DIR/smm-mvc-modernization-plan.md"
IMPLEMENTATION_PLAN="$DOCS_DIR/smm-complete-implementation-plan.md"
LOG_FILE="_dev/docs/.tracking.log"
ALERT_FILE="_dev/docs/.alerts.json"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Functions
log_message() {
    local level=$1
    local message=$2
    local timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    echo "[$timestamp] [$level] $message" >> "$LOG_FILE"
    echo -e "${GREEN}[$timestamp]${NC} ${YELLOW}[$level]${NC} $message"
}

check_file_exists() {
    local file=$1
    if [[ ! -f "$file" ]]; then
        log_message "ERROR" "Missing required file: $file"
        create_alert "MISSING_FILE" "$file" "Critical documentation file is missing"
        return 1
    fi
    return 0
}

check_file_updated() {
    local file=$1
    local max_age_days=7
    
    if [[ -f "$file" ]]; then
        local file_age_seconds=$(( $(date +%s) - $(stat -c %Y "$file" 2>/dev/null || stat -f %m "$file" 2>/dev/null || echo 0) ))
        local max_age_seconds=$(( max_age_days * 24 * 60 * 60 ))
        
        if [[ $file_age_seconds -gt $max_age_seconds ]]; then
            log_message "WARNING" "File $file has not been updated in $max_age_days days"
            create_alert "STALE_FILE" "$file" "File has not been updated in $max_age_days days"
            return 1
        fi
    fi
    return 0
}

check_task_progress() {
    local file=$1
    local incomplete_tasks=0
    local total_tasks=0
    
    if [[ -f "$file" ]]; then
        total_tasks=$(grep -c "^- \\[" "$file" 2>/dev/null || echo 0)
        incomplete_tasks=$(grep -c "^- \\[ \\]" "$file" 2>/dev/null || echo 0)
        
        if [[ $total_tasks -gt 0 ]]; then
            local completion_rate=$(( (total_tasks - incomplete_tasks) * 100 / total_tasks ))
            log_message "INFO" "File $file: $completion_rate% complete ($incomplete_tasks/$total_tasks tasks remaining)"
            
            if [[ $completion_rate -lt 25 ]]; then
                create_alert "LOW_PROGRESS" "$file" "Task completion rate is below 25% ($completion_rate%)"
            fi
        fi
    fi
}

create_alert() {
    local alert_type=$1
    local file=$2
    local message=$3
    local timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    
    # Create alerts file if it doesn't exist
    if [[ ! -f "$ALERT_FILE" ]]; then
        echo "[]" > "$ALERT_FILE"
    fi
    
    # Add new alert
    local alert="{\"timestamp\":\"$timestamp\",\"type\":\"$alert_type\",\"file\":\"$file\",\"message\":\"$message\"}"
    jq ". += [$alert]" "$ALERT_FILE" > "${ALERT_FILE}.tmp" && mv "${ALERT_FILE}.tmp" "$ALERT_FILE"
    
    echo -e "${RED}ALERT${NC}: $alert_type - $message (File: $file)"
}

generate_status_report() {
    echo "=== SMM Documentation Status Report ==="
    echo "Generated at: $(date '+%Y-%m-%d %H:%M:%S')"
    echo ""
    
    echo "File Status:"
    for file in "$MODERNIZATION_PLAN" "$IMPLEMENTATION_PLAN"; do
        if [[ -f "$file" ]]; then
            local last_modified=$(stat -c %y "$file" 2>/dev/null || stat -f %Sm "$file" 2>/dev/null || echo "Unknown")
            echo "  ✓ $file (Last modified: $last_modified)"
        else
            echo "  ✗ $file (MISSING)"
        fi
    done
    
    echo ""
    echo "Recent Alerts:"
    if [[ -f "$ALERT_FILE" ]]; then
        jq -r '.[-5:] | .[] | "  - [\(.timestamp)] \(.type): \(.message)"' "$ALERT_FILE" 2>/dev/null || echo "  No alerts available"
    else
        echo "  No alerts file found"
    fi
    
    echo ""
    echo "=== End of Report ==="
}

# Main execution
main() {
    echo "Starting SMM Documentation Tracking and Alert System..."
    
    # Create necessary directories and files
    mkdir -p "$DOCS_DIR"
    touch "$LOG_FILE"
    
    log_message "INFO" "Starting documentation tracking check"
    
    # Check if required files exist
    local files_ok=true
    for file in "$MODERNIZATION_PLAN" "$IMPLEMENTATION_PLAN"; do
        if ! check_file_exists "$file"; then
            files_ok=false
        fi
    done
    
    # If files exist, perform additional checks
    if [[ "$files_ok" == true ]]; then
        for file in "$MODERNIZATION_PLAN" "$IMPLEMENTATION_PLAN"; do
            check_file_updated "$file"
            check_task_progress "$file"
        done
        log_message "INFO" "All documentation files are present and being tracked"
    else
        log_message "ERROR" "Some required documentation files are missing"
    fi
    
    # Generate status report
    generate_status_report
    
    log_message "INFO" "Documentation tracking check completed"
}

# Run main function if script is executed directly
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main "$@"
fi