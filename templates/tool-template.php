<?php
/**
 * Generic Tool Template
 * Template Name: SmartKitchen Tool
 */
if (!defined('ABSPATH')) {
    exit;
}

get_header();

$tool_id = get_post_meta(get_the_ID(), '_sks_tool_id', true);
?>

<div class="sks-tool-wrapper">
    <div class="sks-container">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            
            <article id="post-<?php the_ID(); ?>" <?php post_class('sks-tool-page'); ?>>
                <header class="sks-tool-page-header">
                    <h1 class="sks-tool-page-title"><?php the_title(); ?></h1>
                    
                    <?php if (has_excerpt()) : ?>
                        <div class="sks-tool-page-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                    <?php endif; ?>
                </header>
                
                <div class="sks-tool-page-content">
                    <?php the_content(); ?>
                </div>
            </article>
            
        <?php endwhile; endif; ?>
    </div>
</div>

<style>
.sks-tool-wrapper {
    min-height: 60vh;
    padding: 40px 0;
}

.sks-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.sks-tool-page {
    background: white;
    border-radius: 8px;
    padding: 40px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.sks-tool-page-header {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #2196f3;
}

.sks-tool-page-title {
    font-size: 2.5em;
    margin: 0 0 15px 0;
    color: #333;
}

.sks-tool-page-excerpt {
    font-size: 1.2em;
    color: #666;
    line-height: 1.6;
}

.sks-tool-page-content {
    line-height: 1.8;
}

@media (max-width: 768px) {
    .sks-tool-page {
        padding: 20px;
    }
    
    .sks-tool-page-title {
        font-size: 2em;
    }
}
</style>

<?php get_footer(); ?>

