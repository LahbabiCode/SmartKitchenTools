# SmartKitchen Suite - Installation Guide

## Prerequisites

Before installing SmartKitchen Suite, ensure your WordPress installation meets these requirements:

- **WordPress**: 5.8 or higher
- **PHP**: 7.4 or higher
- **MySQL**: 5.6 or higher
- **Memory Limit**: 128MB or more recommended
- **Google Gemini API Key**: Required for AI features

## Installation Methods

### Method 1: WordPress Admin (Recommended)

1. **Download the Plugin**
   - Download the plugin zip file from the distribution

2. **Upload via WordPress**
   - Log in to your WordPress admin panel
   - Navigate to **Plugins → Add New**
   - Click **Upload Plugin**
   - Choose the downloaded zip file
   - Click **Install Now**

3. **Activate the Plugin**
   - Click **Activate Plugin** after installation
   - Database tables will be created automatically

### Method 2: FTP Upload

1. **Extract the Plugin**
   - Extract the downloaded zip file
   - You should see a `smartkitchen-suite` folder

2. **Upload via FTP**
   - Connect to your website via FTP
   - Navigate to `/wp-content/plugins/`
   - Upload the `smartkitchen-suite` folder

3. **Activate**
   - Log in to WordPress admin
   - Go to **Plugins**
   - Find "SmartKitchen Suite"
   - Click **Activate**

### Method 3: Git Clone (for Developers)

```bash
cd /path/to/wordpress/wp-content/plugins
git clone https://github.com/smartkitchen/suite.git smartkitchen-suite
cd smartkitchen-suite
npm install
npm run build
```

Then activate from WordPress admin.

## Initial Setup

### 1. Configure API Key

**Important**: The AI features require a Google Gemini API key.

1. Go to **SmartKitchen → Settings**
2. Enter your Google Gemini API key
3. Click **Save Settings**

**Getting a Google Gemini API Key**:
- Visit Google AI Studio: https://aistudio.google.com/
- Sign in with your Google account
- Create a new API key
- Copy the key and paste it in settings

### 2. Explore Built-in Tools

1. Navigate to **SmartKitchen → All Tools**
2. You'll see 10+ pre-built tools:
   - BMI Calculator
   - Recipe Generator
   - Meal Planner
   - Grocery List
   - Temperature Converter
   - And more!

3. Enable/disable tools as needed

### 3. Create Tool Pages

For each tool you want to use on your site:

1. Find the tool in **All Tools**
2. Click **Create Page**
3. The page will be automatically generated
4. Tools are now accessible on the frontend

### 4. Add Tools to Menu (Optional)

1. Go to **SmartKitchen → Settings**
2. Enable **Auto-add to Menu**
3. Or manually add tool pages via **Appearance → Menus**

## Verification

### Check Installation

1. **Check Database Tables**
   ```sql
   SHOW TABLES LIKE '%sks_%';
   ```
   You should see:
   - wp_sks_tools
   - wp_sks_analytics
   - wp_sks_ai_cache

2. **Check Plugin Status**
   - Go to **Plugins → Installed Plugins**
   - SmartKitchen Suite should be active

3. **Test a Tool**
   - Create a test page with shortcode: `[sks_tool id="bmi-calculator"]`
   - Visit the page and verify the tool loads

### Common Issues

#### Issue: "Database tables not created"

**Solution**: 
- Deactivate and reactivate the plugin
- Check file permissions on `/wp-content/plugins/smartkitchen-suite/`

#### Issue: "AI features not working"

**Solution**:
- Verify API key is correct in **Settings**
- Check API key has proper permissions
- Ensure your server can make HTTPS requests to `generativelanguage.googleapis.com`

#### Issue: "Tools not showing on frontend"

**Solution**:
- Clear WordPress cache
- Check tool is enabled in **All Tools**
- Verify shortcode syntax: `[sks_tool id="tool-id"]`

#### Issue: "JavaScript errors in admin"

**Solution**:
- Clear browser cache
- Run `npm install && npm run build` if development version
- Check browser console for specific errors

## Post-Installation

### Recommended Steps

1. **Generate Your First AI Tool**
   - Go to **SmartKitchen → AI Generator**
   - Enter a description like "Create a pasta calculator"
   - Click **Generate Tool**
   - Watch the magic happen!

2. **Configure Analytics**
   - Enable/disable tracking in **Settings**
   - View stats in **Analytics**

3. **Customize Your Experience**
   - Toggle dark mode
   - Organize tools by category
   - Set up menu integration

4. **Test Performance**
   - Check page load times
   - Monitor database queries
   - Test on mobile devices

## Updates

### Automatic Updates

The plugin supports WordPress automatic updates:
- Go to **Plugins → Installed Plugins**
- Check "Enable auto-updates" for SmartKitchen Suite

### Manual Updates

1. Download the latest version
2. Deactivate current version
3. Delete old version (data is preserved in database)
4. Upload and activate new version

## Uninstallation

### Standard Uninstall

1. **Via WordPress Admin**
   - Go to **Plugins**
   - Click **Deactivate** for SmartKitchen Suite
   - Click **Delete**

2. **Database Cleanup**
   - Database tables and options are automatically removed
   - No manual cleanup required

### Manual Cleanup (if needed)

If you need to completely remove traces:

```sql
-- Drop tables
DROP TABLE wp_sks_tools;
DROP TABLE wp_sks_analytics;
DROP TABLE wp_sks_ai_cache;

-- Delete options
DELETE FROM wp_options WHERE option_name LIKE 'sks_%';
```

## Support

- **Documentation**: See README.md
- **Issue Reports**: GitHub Issues
- **Community Forum**: Coming soon
- **Email Support**: support@smartkitchen.suite

## Next Steps

Now that you're installed:

1. ✅ Read the [User Guide](README.md#usage)
2. ✅ Generate your first AI tool
3. ✅ Create pages for your favorite tools
4. ✅ Share with your users!
5. ✅ Consider upgrading for more features

Enjoy SmartKitchen Suite! 🎉

