$(document).ready(function() {
    // Tab functionality
    $('.tab').click(function() {
        const tabId = $(this).data('tab');
        
        $('.tab').removeClass('active');
        $(this).addClass('active');
        
        $('.tab-content').removeClass('active');
        $('#' + tabId).addClass('active');
    });

    // Date filter functionality
    $('#filter-btn').click(function() {
        const dateFilter = $('#date-filter').val();
        
        if (dateFilter) {
            $('tr[data-date]').each(function() {
                $(this).toggle($(this).data('date') === dateFilter);
            });
        }
    });

    // Reset filter
    $('#reset-btn').click(function() {
        $('#date-filter').val('');
        $('tr[data-date]').show();
    });

    // Cancel button confirmation
    $('.cancel-btn').click(function(e) {
        if (!confirm('Are you sure you want to cancel this reservation?')) {
            e.preventDefault();
        }
    });

    // Confirm button confirmation
    $('.confirm-btn').click(function(e) {
        if (!confirm('Are you sure you want to confirm this reservation?')) {
            e.preventDefault();
        }
    });

    // Email copy functionality
    $('.copy-email').click(function() {
        const emailText = $(this).data('email');
        navigator.clipboard.writeText(emailText)
            .then(() => {
                const originalText = $(this).text();
                $(this).text('Copied!');
                setTimeout(() => {
                    $(this).text(originalText);
                }, 2000);
            })
            .catch(err => {
                console.error('Failed to copy email: ', err);
                alert('Failed to copy email to clipboard');
            });
    });

    // Send email functionality
    $('.send-email').click(function() {
        const emailAddress = $(this).data('email');
        const subject = encodeURIComponent('Coffee Pop-up Reservation');
        window.location.href = `mailto:${emailAddress}?subject=${subject}`;
    });

    // Search filter
    $('#search-input').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('.reservation-table tbody tr').each(function() {
            const rowText = $(this).text().toLowerCase();
            const emailText = $(this).find('.reservation-email').text().toLowerCase();
            $(this).toggle(rowText.includes(searchTerm) || emailText.includes(searchTerm));
        });
    });

    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Bootstrap tooltips
    if (typeof $().tooltip === 'function') {
        $('[data-toggle="tooltip"]').tooltip();
    }

    // Responsive email display
    function adjustEmailDisplay() {
        if (window.innerWidth < 768) {
            $('.reservation-email').each(function() {
                const fullEmail = $(this).attr('data-email');
                if (fullEmail && fullEmail.length > 15) {
                    const shortEmail = fullEmail.substring(0, 12) + '...';
                    $(this).text(shortEmail);
                    $(this).attr('title', fullEmail);
                }
            });
        } else {
            $('.reservation-email').each(function() {
                const fullEmail = $(this).attr('data-email');
                if (fullEmail) {
                    $(this).text(fullEmail);
                }
            });
        }
    }

    adjustEmailDisplay();
    $(window).resize(adjustEmailDisplay);
});
