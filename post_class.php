<?php

class Post_Widget extends WP_Widget
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'scripts'));

        parent::__construct(
            'mypost',
            'Single Post Widget',
            [
                'description' => 'Widget para incluir uma pequena descrição para Posts, Páginas ou Categorias'
            ]
        );
    }

    public function scripts()
    {
       wp_enqueue_script( 'media-upload' );
       wp_enqueue_media();
       wp_enqueue_script('script', plugins_url('media_manager.js', __FILE__), array('jquery'));
       wp_register_style('styless', plugins_url('styles.css', __FILE__));
       wp_enqueue_style('styless' );
    }


    public function form($instance)
    {
        if (isset($instance)) {
            $image      = esc_url($instance['image']);
            $title      = esc_attr($instance['title']);
            $textarea   = esc_textarea($instance['textarea']);
            $label      = (isset($instance['wlabel'])) ? $instance['wlabel'] : '';
            $type       = (isset($instance['wtype'])) ? $instance['wtype'] : '';
            $page       = (isset($instance['wpage'])) ? $instance['wpage'] : '';
            $post       = (isset($instance['wpost'])) ? $instance['wpost'] : '';
            $category   = (isset($instance['wcategory'])) ? $instance['wcategory'] : '';
    
            $page = [
                'name' => $this->get_field_name('wpage'),
                'id' => 'page-link',
                'class' => 'widget-field ' . (($type != 'page') ? 'hidden' : ''),
                'show_option_none' => 'Selecione uma Página',
                'option_none_value' => 'false',
                'selected' => $page
            ];
    
            $category = [
                'name' => $this->get_field_name('wcat'),
                'id' => 'category-link',
                'class' => 'widget-field ' . (($type != 'category') ? 'hidden' : ''),
                'show_option_none' => 'Selecione uma Categoria',
                'option_none_value' => 'false',
                'child_of' => 0,
                'hide_empty' => 0,
                'selected' => $category
            ];
    
            $posts = [
                'name' => $this->get_field_name('wpost'),
                'id' => 'post-link',
                'class' => 'widget-field ' . (($type != 'post') ? 'hidden' : ''),
                'show_option_none' => 'Selecione um Post',
                'option_none_value' => 'false',
                'selected' => $post
            ];
        } else {
            $image     = '';
            $title     = '';
            $textarea  = '';
            $label     = '';
            $type      = '';
            $page      = [];
            $category  = [];
            $posts     = [];
        }

        ?>
            <p>
                <button class="upload_image_button pw-button button">Selecione uma imagem</button>
                <img class="pw-img-preview" src="<?= esc_url( $image ) ?>" alt="">

                <input id="<?= $this->get_field_id('image'); ?>" name="<?= $this->get_field_name( 'image' ); ?>" type="hidden" value="<?= esc_url( $image ) ?>" />
            </p>

            <p>
                <label for="<?= $this->get_field_id('title') ?>">
                    <?php _e('Título:', 'wp_widget_plugin') ?>
                </label>
                <input class="widefat" id="<?= $this->get_field_id('title') ?>" name="<?= $this->get_field_name('title'); ?>" type="text" value="<?= $title; ?>" />
            </p>

            <p>
                <label for="<?php echo $this->get_field_id('textarea'); ?>"><?php _e('Descrição:', 'wp_widget_plugin'); ?></label>
                <textarea class="widefat" id="<?php echo $this->get_field_id('textarea'); ?>" name="<?php echo $this->get_field_name('textarea'); ?>"><?php echo $textarea; ?></textarea>
            </p>

            <label for="link">Link:</label>
            <input type="text" id="link" class="widget-field widefat" placeholder="Label" value="<?= $instance['wlabel'] ?>" name="<?= $this->get_field_name('wlabel') ?>">

            <select id="tipo" class="widget-field widefat" name="<?= $this->get_field_name('wtype') ?>">
                <option disabled <?= (!$type) ? 'selected' : '' ?>>Tipo</option>
                <option value="post" <?= ($type == 'post') ? 'selected' : '' ?>>Post</option>
                <option value="page" <?= ($type == 'page') ? 'selected' : '' ?>>Página</option>
                <option value="category" <?= ($type == 'category') ? 'selected' : '' ?>>Categoria</option>
            </select>
        <?php

        wp_dropdown_pages($page);
        wp_dropdown_categories($category);
        $this->wp_dropdown_posts($posts);
    }

    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;

        $instance['image']      = $new_instance['image'];
        $instance['title']      = strip_tags($new_instance['title']);
        $instance['textarea']   = strip_tags($new_instance['textarea']);

        if (isset($new_instance['wlabel'])) {
            $instance['wlabel'] = $new_instance['wlabel'];
        }

        if (isset($new_instance['wtype'])) {
            $instance['wtype'] = $new_instance['wtype'];
        }

        if (isset($new_instance['wpage']) && (int) $new_instance['wpage'] >= 0) {
            $instance['wpage'] = $new_instance['wpage'];
        }

        if (isset($new_instance['wcat']) && (int) $new_instance['wcat'] >= 0) {
            $instance['wcat'] = $new_instance['wcat'];
        }

        if (isset($new_instance['wpost']) && (int) $new_instance['wpost'] >= 0) {
            $instance['wpost'] = $new_instance['wpost'];
        }

        return $instance;
    }

    public function widget($args, $instance)
    {
        extract($args);

        $title      = apply_filters('widget-title', $instance['title']);
        $text       = $instance['text'];
        $textarea   = $instance['textarea'];
        $image      = $instance['image'];
        $link       = '';
        
        if (isset($instance['wcat']) && $instance['wcat'] != 'false') {
            $page = get_category($instance['wcat']);
            $link = get_category_link( $page );
        } elseif (isset($instance['wpage']) && $instance['wpage'] != 'false') {
            $page = get_post($instance['wpage']);
            $link = get_permalink($page);
        } elseif (isset($instance['wpost']) && $instance['wpost'] != 'false') {
            $page = get_post($instance['wpost']);
            $link = get_permalink($page);
        } else return;

        echo $before_widget;

        if ($image) {
            echo '<img src="' . $image . '">';
        }

        if ($title) {
            echo $before_title . $title . $after_title;
        }

        if ($textarea) {
            echo '<p>' . $textarea . '</p>';
        }

        if ($link && $instance['wlabel']) {
            echo '<a href="' . $link .'" class="pw-link">' . $instance['wlabel'] . '</a>';
        }

        echo $after_widget;
    }

    function wp_dropdown_posts($args)
    {
        $posts = get_posts([
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => -1
        ]);

        ?>
            <select id="<?= $args['id'] ?>" class="<?= $args['class'] ?>" name="<?= $args['name'] ?>">
                <?php if(isset($args['show_option_none'])): ?>
                    <option value="<?= ($args['option_none_value']) ? $args['option_none_value'] : '' ?>">
                        <?= $args['show_option_none'] ?>
                    </option>
                    <?php foreach($posts as $post): ?>
                        <option value="<?= $post->ID ?>" <?= ($args['selected'] == $post->ID ? 'selected' : '')?>><?= $post->post_title ?></option>
                    <?php endforeach?>
                <?php endif ?>
            </select>
        <?php
    }
}

add_action('widgets_init', create_function('', 'return register_widget("Post_Widget");'));
