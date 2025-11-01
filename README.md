# SmartKitchen Suite

A professional, scalable, AI-powered WordPress plugin offering 60+ intelligent tools for cooking, kitchen management, and healthy nutrition.

## 🌟 Features

### AI-Powered Generation
- **Gemini Flash Integration**: Automatically generate new tools, recipes, meal plans, and content using Google's Gemini Flash AI
- **Smart Tool Creation**: One-click tool generation based on natural language descriptions
- **Content Enhancement**: AI-powered suggestions and improvements for existing tools

### Tool Categories
- **Cooking Tools**: Recipe generator, temperature converter, ingredient converter, cooking timer, etc.
- **Nutrition Tools**: BMI calculator, calorie counter, macro calculator, meal planning
- **Health & Wellness**: Sleep tracking, allergen finder, vitamin info, balanced meal generator
- **Community Tools**: Recipe sharing, forums, ratings, favorites, AI cooking assistant
- **Shopping & Planning**: Grocery list generator, weekly meal planner, ingredient substitution tool

### Modern Admin Dashboard
- **React-based Interface**: Sleek, responsive admin dashboard
- **Tool Management**: Enable/disable tools, create pages, manage menu placement
- **AI Generator Panel**: Generate new tools on-demand
- **Analytics Dashboard**: Track tool usage and user engagement
- **Dark/Light Mode**: Toggle between themes
- **SEO-Friendly**: Auto-generated pages with proper meta tags

### Developer-Friendly
- **Modular Architecture**: Clean, extendable codebase
- **REST API**: Full REST API for tool management
- **Shortcode System**: Easy integration via shortcodes
- **Translation Ready**: Full i18n support
- **Security**: Sanitized inputs, escaped outputs, nonce validation

## 📦 Installation

### Requirements
- WordPress 5.8 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher
- Google Gemini API key (for AI features)

### Setup Steps

1. **Upload Plugin**
   - Download the plugin
   - Upload `smartkitchen-suite` folder to `/wp-content/plugins/`
   - Activate via WordPress admin

2. **Database Setup**
   - Database tables are created automatically on activation
   - No manual setup required

3. **Configure API Key**
   - Navigate to SmartKitchen → Settings
   - Enter your Google Gemini API key
   - Save settings

4. **Start Using**
   - Browse built-in tools in SmartKitchen → All Tools
   - Generate new tools with AI
   - Create pages for your tools

## 🚀 Usage

### Using Shortcodes

Add any tool to your posts or pages using shortcodes:

```
[sks_tool id="bmi-calculator"]
[sks_tool slug="recipe-generator"]
```

### Creating Tool Pages

1. Go to SmartKitchen → All Tools
2. Click "Create Page" on any tool
3. Page is automatically created with shortcode

### Generating New Tools with AI

1. Navigate to SmartKitchen → AI Generator
2. Enter a description of the tool you want
3. Click "Generate Tool"
4. AI creates the tool and optionally creates a page

### Managing Tools

- **Enable/Disable**: Toggle tools on or off
- **Customize**: Edit tool name, description, icon
- **Menu Placement**: Control where tools appear in navigation
- **Analytics**: Monitor usage statistics

## 🛠️ Development

### Directory Structure

```
smartkitchen-suite/
├── smartkitchen-suite.php    # Main plugin file
├── includes/                  # Core PHP classes
│   ├── class-database.php
│   ├── class-tool-registry.php
│   ├── class-tool-loader.php
│   ├── class-shortcodes.php
│   ├── class-utilities.php
│   └── class-rest-api.php
├── admin/                     # Admin interface
│   ├── class-admin.php
│   ├── build/
│   └── assets/
├── ai/                        # AI integration
│   ├── class-gemini-integration.php
│   └── class-tool-generator.php
├── tools/                     # Tool implementations
│   ├── class-tool-bmi-calculator.php
│   ├── class-tool-recipe-generator.php
│   ├── class-tool-meal-planner.php
│   ├── class-tool-grocery-list.php
│   └── class-tool-temperature-converter.php
├── templates/                 # Frontend templates
├── assets/                    # CSS, JS, icons
└── README.md
```

### Creating Custom Tools

Create a new tool class:

```php
class SKS_Tool_Custom_Tool {
    
    public static function register() {
        SKS_Tool_Registry::register('custom-tool', [
            'name' => 'My Custom Tool',
            'slug' => 'custom-tool',
            'category' => 'cooking',
            'description' => 'Tool description',
            'icon' => '🔧',
            'class_name' => __CLASS__,
            'shortcode' => 'sks_custom_tool',
            'settings' => []
        ]);
    }
    
    public function render($args = []) {
        // Tool output
    }
}

SKS_Tool_Custom_Tool::register();
```

### Using the REST API

**Get all tools:**
```
GET /wp-json/sks/v1/tools
```

**Generate tool with AI:**
```
POST /wp-json/sks/v1/ai/generate-tool
{
    "prompt": "Create a pasta calculator tool",
    "create_page": true
}
```

## 🔒 Security

- All inputs are sanitized
- All outputs are escaped
- Nonce verification on all forms
- Capability checks on admin functions
- SQL injection prevention via prepared statements
- XSS prevention via WordPress security functions

## 🌍 Localization

The plugin is translation-ready. Translations are located in `/languages/`.

Supported languages:
- English (default)
- Ready for custom translations

## 📊 Analytics

Track tool usage automatically:
- Total uses per tool
- Unique users
- Daily statistics
- Export capabilities

## 🤝 Contributing

Contributions are welcome! Please ensure:
- Code follows WordPress coding standards
- All inputs are sanitized
- All outputs are escaped
- Functions are documented
- Security best practices are followed

## 📝 License

GPL v2 or later

## 🙏 Credits

Built with:
- WordPress
- Google Gemini Flash AI
- React
- Modern web technologies

## 📞 Support

For support, feature requests, or bug reports, please visit our support page.

## 🎯 Roadmap

- [ ] Additional 50+ tools
- [ ] Mobile app integration
- [ ] Social sharing features
- [ ] Recipe import/export
- [ ] Voice assistant integration
- [ ] Advanced analytics
- [ ] Multi-language recipes
- [ ] Community marketplace

## 🏆 Recognition

SmartKitchen Suite - Empowering healthier cooking and smarter kitchen management through AI.

