$(document).ready(function() {
    // Tab functionality
    $('.tab').click(function() {
        const tabId = $(this).data('tab');
        
        // Update active tab button
        $('.tab').removeClass('active');
        $(this).addClass('active');
        
        // Show selected tab content, hide others
        $('.tab-content').removeClass('active');
        $('#' + tabId).addClass('active');
    });
    
    // Date filter functionality
    $('#filter-btn').click(function() {
        const dateFilter = $('#date-filter').val();
        
        if (dateFilter) {
            // Hide rows that don't match the selected date
            $('tr[data-date]').hide();
            $('tr[data-date="' + dateFilter + '"]').show();
        }
    });
    
    // Reset filter
    $('#reset-btn').click(function() {
        $('#date-filter').val('');
        $('tr[data-date]').show();
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Search functionality (optional enhancement)
    $('#search-input').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        
        $('tbody tr').each(function() {
            const rowText = $(this).text().toLowerCase();
            if (rowText.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    
    // Export functionality (if needed)
    $('#export-btn').click(function() {
        // Basic CSV export functionality
        let csv = 'ID,Date,Time,Event,Location,Contact Email,Status\n';
        
        $('.reservation-table tbody tr:visible').each(function() {
            const row = [];
            $(this).find('td').each(function() {
                row.push('"' + $(this).text().replace(/"/g, '""') + '"');
            });
            csv += row.join(',') + '\n';
        });
        
        // Download CSV
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.setAttribute('hidden', '');
        a.setAttribute('href', url);
        a.setAttribute('download', 'staff_reservations_' + new Date().toISOString().split('T')[0] + '.csv');
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });
    
    // Print functionality
    $('#print-btn').click(function() {
        window.print();
    });
    
    // Refresh data functionality
    $('#refresh-btn').click(function() {
        location.reload();
    });
});