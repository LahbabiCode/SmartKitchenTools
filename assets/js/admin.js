/**
 * SmartKitchen Suite - Admin JavaScript
 */
(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Dark mode toggle
        if (sksAdmin.darkMode) {
            $('body').addClass('sks-dark-mode');
        }
        
        // Tool toggle functionality
        $('.sks-tool-toggle input').on('change', function() {
            const toolId = $(this).closest('.sks-tool-card').data('tool-id');
            const isActive = $(this).is(':checked');
            
            // Send AJAX request
            $.ajax({
                url: sksAdmin.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'sks_toggle_tool',
                    tool_id: toolId,
                    is_active: isActive,
                    nonce: sksAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        console.log('Tool toggled successfully');
                    }
                }
            });
        });
        
        // Delete tool confirmation
        $('.sks-delete-tool').on('click', function(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to delete this tool? This action cannot be undone.')) {
                return;
            }
            
            const toolId = $(this).data('tool-id');
            
            $.ajax({
                url: sksAdmin.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'sks_delete_tool',
                    tool_id: toolId,
                    nonce: sksAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                }
            });
        });
        
        // Bulk actions
        $('#sks-bulk-action-button').on('click', function() {
            const action = $('#sks-bulk-action-select').val();
            const selectedTools = [];
            
            $('input[name="sks-tool-checkbox"]:checked').each(function() {
                selectedTools.push($(this).val());
            });
            
            if (selectedTools.length === 0) {
                alert('Please select at least one tool.');
                return;
            }
            
            $.ajax({
                url: sksAdmin.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'sks_bulk_action',
                    bulk_action: action,
                    tools: selectedTools,
                    nonce: sksAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                }
            });
        });
        
        // Search functionality
        $('#sks-tool-search').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            
            $('.sks-tool-card').each(function() {
                const toolName = $(this).find('.sks-tool-name').text().toLowerCase();
                const toolDesc = $(this).find('.sks-tool-description').text().toLowerCase();
                
                if (toolName.includes(searchTerm) || toolDesc.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
        
        // Category filter
        $('#sks-category-filter').on('change', function() {
            const category = $(this).val();
            
            if (category === 'all') {
                $('.sks-tool-card').show();
            } else {
                $('.sks-tool-card').each(function() {
                    if ($(this).data('category') === category) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
        });
        
        // Copy shortcode to clipboard
        $('.sks-copy-shortcode').on('click', function(e) {
            e.preventDefault();
            
            const shortcode = $(this).data('shortcode');
            const temp = $('<input>');
            $('body').append(temp);
            temp.val(shortcode).select();
            document.execCommand('copy');
            temp.remove();
            
            // Show feedback
            $(this).text('Copied!');
            setTimeout(() => {
                $(this).text('Copy');
            }, 2000);
        });
        
        // AI content generation
        $('#sks-generate-content').on('click', function() {
            const toolId = $(this).data('tool-id');
            const prompt = $('#sks-ai-prompt').val();
            
            if (!prompt) {
                alert('Please enter a prompt.');
                return;
            }
            
            $(this).prop('disabled', true).text('Generating...');
            
            $.ajax({
                url: sksAdmin.restUrl + 'ai/generate-content',
                method: 'POST',
                headers: {
                    'X-WP-Nonce': sksAdmin.nonce
                },
                data: JSON.stringify({ prompt: prompt }),
                contentType: 'application/json',
                success: function(response) {
                    $('#sks-ai-output').val(response.content);
                },
                complete: function() {
                    $('#sks-generate-content').prop('disabled', false).text('Generate');
                }
            });
        });
        
    });
    
})(jQuery);

