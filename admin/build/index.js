(function() {
    'use strict';
    
    // React-like component system for WordPress admin
    const { useState, useEffect, useRef } = window.React || {};
    
    // Main Dashboard Component
    function SmartKitchenDashboard() {
        const [tools, setTools] = useState([]);
        const [loading, setLoading] = useState(true);
        const [searchTerm, setSearchTerm] = useState('');
        const [categoryFilter, setCategoryFilter] = useState('all');
        const [selectedTool, setSelectedTool] = useState(null);
        
        useEffect(() => {
            loadTools();
        }, []);
        
        const loadTools = async () => {
            try {
                const response = await fetch(`${sksData.apiUrl}tools`, {
                    headers: {
                        'X-WP-Nonce': sksData.nonce
                    }
                });
                const data = await response.json();
                setTools(data);
                setLoading(false);
            } catch (error) {
                console.error('Error loading tools:', error);
                setLoading(false);
            }
        };
        
        const toggleTool = async (toolId, currentStatus) => {
            try {
                const response = await fetch(`${sksData.apiUrl}tools/${toolId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': sksData.nonce
                    },
                    body: JSON.stringify({
                        is_active: !currentStatus
                    })
                });
                
                if (response.ok) {
                    loadTools();
                }
            } catch (error) {
                console.error('Error toggling tool:', error);
            }
        };
        
        const createToolPage = async (toolId) => {
            // Implementation for creating tool page
            alert('Creating page for tool: ' + toolId);
        };
        
        const filteredTools = tools.filter(tool => {
            const matchesSearch = tool.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                                tool.description.toLowerCase().includes(searchTerm.toLowerCase());
            const matchesCategory = categoryFilter === 'all' || tool.category === categoryFilter;
            return matchesSearch && matchesCategory;
        });
        
        if (loading) {
            return React.createElement('div', { className: 'sks-loading' }, 
                React.createElement('div', {}, 'Loading tools...')
            );
        }
        
        return React.createElement('div', { className: 'sks-admin-dashboard' },
            React.createElement(ToolHeader, { 
                searchTerm, 
                setSearchTerm, 
                categoryFilter, 
                setCategoryFilter 
            }),
            React.createElement(ToolGrid, { 
                tools: filteredTools, 
                toggleTool, 
                createToolPage, 
                setSelectedTool 
            }),
            selectedTool && React.createElement(ToolDetails, { 
                tool: selectedTool, 
                onClose: () => setSelectedTool(null) 
            })
        );
    }
    
    // Tool Header Component
    function ToolHeader({ searchTerm, setSearchTerm, categoryFilter, setCategoryFilter }) {
        const categories = [{ id: 'all', name: 'All Categories' }, ...Object.entries(sksData.categories).map(([id, name]) => ({ id, name }))];
        
        return React.createElement('div', { className: 'sks-tool-header' },
            React.createElement('input', {
                type: 'text',
                placeholder: 'Search tools...',
                value: searchTerm,
                onChange: (e) => setSearchTerm(e.target.value),
                className: 'sks-search-input'
            }),
            React.createElement('select', {
                value: categoryFilter,
                onChange: (e) => setCategoryFilter(e.target.value),
                className: 'sks-category-filter'
            }, categories.map(cat => 
                React.createElement('option', { key: cat.id, value: cat.id }, cat.name)
            )),
            React.createElement('button', {
                className: 'sks-btn sks-btn-primary',
                onClick: () => window.location.href = 'admin.php?page=smartkitchen-ai-generator'
            }, 'Generate New Tool with AI')
        );
    }
    
    // Tool Grid Component
    function ToolGrid({ tools, toggleTool, createToolPage, setSelectedTool }) {
        if (tools.length === 0) {
            return React.createElement('div', { className: 'sks-empty-state' },
                React.createElement('p', {}, 'No tools found. Create your first tool using AI!')
            );
        }
        
        return React.createElement('div', { className: 'sks-tool-grid' },
            tools.map(tool => React.createElement(ToolCard, {
                key: tool.tool_id,
                tool,
                toggleTool,
                createToolPage,
                onClick: () => setSelectedTool(tool)
            }))
        );
    }
    
    // Tool Card Component
    function ToolCard({ tool, toggleTool, createToolPage, onClick }) {
        return React.createElement('div', { className: 'sks-tool-card' },
            React.createElement('div', { className: 'sks-tool-card-header' },
                React.createElement('div', {},
                    React.createElement('span', { className: 'sks-tool-icon' }, tool.icon || '🔧'),
                    React.createElement('h3', {}, tool.name)
                ),
                React.createElement('label', { className: 'sks-tool-toggle' },
                    React.createElement('input', {
                        type: 'checkbox',
                        checked: tool.is_active === 1 || tool.is_active === true,
                        onChange: () => toggleTool(tool.tool_id, tool.is_active)
                    }),
                    React.createElement('span', { className: 'sks-tool-toggle-slider' })
                )
            ),
            React.createElement('div', { className: 'sks-tool-body' },
                React.createElement('span', { className: 'sks-tool-category' }, 
                    sksData.categories[tool.category] || tool.category
                ),
                React.createElement('p', { className: 'sks-tool-description' }, tool.description || 'No description'),
                React.createElement('div', { className: 'sks-tool-stats' },
                    React.createElement('span', {}, `Used ${tool.usage_count || 0} times`)
                )
            ),
            React.createElement('div', { className: 'sks-tool-footer' },
                React.createElement('button', {
                    className: 'sks-btn sks-btn-secondary',
                    onClick: onClick
                }, 'Details'),
                React.createElement('button', {
                    className: 'sks-btn sks-btn-secondary',
                    onClick: () => createToolPage(tool.tool_id)
                }, 'Create Page'),
                tool.ai_generated === 1 && React.createElement('span', { 
                    className: 'sks-ai-badge' 
                }, '✨ AI')
            )
        );
    }
    
    // Tool Details Modal
    function ToolDetails({ tool, onClose }) {
        return React.createElement('div', { className: 'sks-modal-overlay', onClick: onClose },
            React.createElement('div', { className: 'sks-modal', onClick: (e) => e.stopPropagation() },
                React.createElement('div', { className: 'sks-modal-header' },
                    React.createElement('h2', {}, tool.name),
                    React.createElement('button', { onClick: onClose }, '×')
                ),
                React.createElement('div', { className: 'sks-modal-body' },
                    React.createElement('p', {}, tool.description),
                    React.createElement('div', { className: 'sks-modal-actions' },
                        React.createElement('button', { className: 'sks-btn sks-btn-primary' }, 'Edit'),
                        React.createElement('button', { className: 'sks-btn sks-btn-danger' }, 'Delete')
                    )
                )
            )
        );
    }
    
    // Initialize the app
    if (document.getElementById('sks-admin-root')) {
        const root = ReactDOM.createRoot(document.getElementById('sks-admin-root'));
        root.render(React.createElement(SmartKitchenDashboard));
    }
})();

