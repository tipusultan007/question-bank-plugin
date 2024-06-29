<?php

class Question_Bank_Plugin
{
    public function run()
    {
        add_action('init', array($this, 'create_question_category_post_type'));
        add_action('init', array($this, 'create_topic_post_type'));
        add_action('add_meta_boxes', array($this, 'add_topic_meta_boxes'));
        add_action('save_post', array($this, 'save_topic_meta'), 10, 2);
        add_shortcode('question_bank', array($this, 'display_question_bank'));
        // add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function create_question_category_post_type()
    {
        $labels = array(
            'name'               => _x('Question Categories', 'post type general name'),
            'singular_name'      => _x('Question Category', 'post type singular name'),
            'menu_name'          => _x('Question Categories', 'admin menu'),
            'name_admin_bar'     => _x('Question Category', 'add new on admin bar'),
            'add_new'            => _x('Add New', 'question category'),
            'add_new_item'       => __('Add New Question Category'),
            'new_item'           => __('New Question Category'),
            'edit_item'          => __('Edit Question Category'),
            'view_item'          => __('View Question Category'),
            'all_items'          => __('All Question Categories'),
            'search_items'       => __('Search Question Categories'),
            'parent_item_colon'  => __('Parent Question Categories:'),
            'not_found'          => __('No question categories found.'),
            'not_found_in_trash' => __('No question categories found in Trash.')
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'question-category'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title', 'editor')
        );

        register_post_type('question_category', $args);
    }

    public function create_topic_post_type()
    {
        $labels = array(
            'name'               => _x('Topics', 'post type general name'),
            'singular_name'      => _x('Topic', 'post type singular name'),
            'menu_name'          => _x('Topics', 'admin menu'),
            'name_admin_bar'     => _x('Topic', 'add new on admin bar'),
            'add_new'            => _x('Add New', 'topic'),
            'add_new_item'       => __('Add New Topic'),
            'new_item'           => __('New Topic'),
            'edit_item'          => __('Edit Topic'),
            'view_item'          => __('View Topic'),
            'all_items'          => __('All Topics'),
            'search_items'       => __('Search Topics'),
            'parent_item_colon'  => __('Parent Topics:'),
            'not_found'          => __('No topics found.'),
            'not_found_in_trash' => __('No topics found in Trash.')
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'topic'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title', 'editor')
        );

        register_post_type('topic', $args);
    }

    public function add_topic_meta_boxes()
    {
        add_meta_box(
            'topic_questions_meta_box',
            'Questions',
            array($this, 'display_topic_questions_meta_box'),
            'topic',
            'normal',
            'high'
        );
        add_meta_box(
            'topic_category_meta_box',
            'Question Category',
            array($this, 'display_topic_category_meta_box'),
            'topic',
            'side',
            'high'
        );
    }
    public function display_topic_category_meta_box($post)
    {
        wp_nonce_field('topic_category_meta_box_nonce', 'topic_category_meta_box_nonce');

        $question_category = get_post_meta($post->ID, 'question_category', true);

        $categories = get_posts(array(
            'post_type' => 'question_category',
            'posts_per_page' => -1,
        ));

?>
        <p>
            <label for="question_category">Select Question Category:</label>
            <select name="question_category" id="question_category">
                <option value="">Select Category</option>
                <?php foreach ($categories as $category) : ?>
                    <option value="<?php echo $category->ID; ?>" <?php selected($question_category, $category->ID); ?>><?php echo $category->post_title; ?></option>
                <?php endforeach; ?>
            </select>
        </p>
    <?php
    }
    public function display_topic_questions_meta_box($post)
    {
        wp_nonce_field('topic_questions_meta_box_nonce', 'topic_questions_meta_box_nonce');

        $questions = get_post_meta($post->ID, 'questions', true);
    ?>

        <div id="questions-repeater">
            <?php if ($questions && is_array($questions)) : ?>
                <?php foreach ($questions as $question) : ?>
                    <div class="repeater-item">
                        <p>
                            <label>Type:</label>
                            <select name="questions[type][]">
                                <option value="Question" <?php selected($question['type'], 'Question'); ?>>Question</option>
                                <option value="Tip" <?php selected($question['type'], 'Tip'); ?>>Tip</option>
                            </select>
                        </p>
                        <p>
                            <label>Title:</label>
                            <input type="text" name="questions[title][]" value="<?php echo esc_attr($question['title']); ?>" />
                        </p>
                        <p>
                            <label>How to Answer:</label>
                            <textarea name="questions[how_to_answer][]"><?php echo esc_textarea($question['how_to_answer']); ?></textarea>
                        </p>
                        <p>
                            <label>Example Answer:</label>
                            <?php
                            $example_answer_content = isset($question['example_answer']) ? $question['example_answer'] : '';
                            $example_answer_id = 'example_answer_' . uniqid();
                            wp_editor($example_answer_content, $example_answer_id, array(
                                'textarea_name' => 'questions[example_answer][]',
                                'textarea_rows' => 5,
                                'editor_class' => 'example-answer-editor',
                                'media_buttons' => false, // Disable media buttons
                            ));
                            ?>
                        </p>
                        <button type="button" class="remove-repeater-item button">Remove</button>
                        <hr>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button type="button" id="add-repeater-item" class="button">Add Question</button>

        <script>
            jQuery(document).ready(function($) {
                $('#add-repeater-item').click(function() {
                    $('#questions-repeater').append(`
                    <div class="repeater-item">
                        <p>
                            <label>Type:</label>
                            <select name="questions[type][]">
                                <option value="Question">Question</option>
                                <option value="Tip">Tip</option>
                            </select>
                        </p>
                        <p>
                            <label>Title:</label>
                            <input type="text" name="questions[title][]" />
                        </p>
                        <p>
                            <label>How to Answer:</label>
                            <textarea name="questions[how_to_answer][]"></textarea>
                        </p>
                        <p>
                            <label>Example Answer:</label>
                            <?php
                            $new_example_answer_id = 'example_answer_' . uniqid();
                            wp_editor('', $new_example_answer_id, array(
                                'textarea_name' => 'questions[example_answer][]',
                                'textarea_rows' => 5,
                                'editor_class' => 'example-answer-editor',
                                'media_buttons' => false, // Disable media buttons
                            ));
                            ?>
                        </p>
                        <button type="button" class="remove-repeater-item button">Remove</button>
                        <hr>
                    </div>
                `);
                });

                $(document).on('click', '.remove-repeater-item', function() {
                    $(this).closest('.repeater-item').remove();
                });
            });
        </script>

    <?php
    }

    public function save_topic_meta($post_id, $post)
    {
        if (!isset($_POST['topic_questions_meta_box_nonce']) || !wp_verify_nonce($_POST['topic_questions_meta_box_nonce'], 'topic_questions_meta_box_nonce')) {
            return $post_id;
        }

        if (!isset($_POST['topic_category_meta_box_nonce']) || !wp_verify_nonce($_POST['topic_category_meta_box_nonce'], 'topic_category_meta_box_nonce')) {
            return $post_id;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        if ('topic' !== $post->post_type) {
            return $post_id;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
        if (isset($_POST['question_category'])) {
            update_post_meta($post_id, 'question_category', sanitize_text_field($_POST['question_category']));
        }
        $questions = array();
        if (isset($_POST['questions'])) {
            foreach ($_POST['questions']['type'] as $key => $value) {
                $questions[] = array(
                    'type' => sanitize_text_field($value),
                    'title' => sanitize_text_field($_POST['questions']['title'][$key]),
                    'how_to_answer' => sanitize_textarea_field($_POST['questions']['how_to_answer'][$key]),
                    'example_answer' => wp_kses_post($_POST['questions']['example_answer'][$key]), // Sanitize as HTML
                );
            }
        }

        update_post_meta($post_id, 'questions', $questions);
    }

    public function display_question_bank()
    {
        $args = array(
            'post_type' => 'question_category',
            'posts_per_page' => -1,
        );
        $query = new WP_Query($args);
        ob_start();
    ?>
        <div id="question-bank">
            <div class="horizontal-tabs">
                <?php if ($query->have_posts()) : ?>
                    <?php while ($query->have_posts()) : $query->the_post(); ?>
                        <button class="tab-button" data-tab="tab-<?php echo get_the_ID(); ?>"><?php the_title(); ?></button>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                <?php endif; ?>
            </div>
            <div class="tab-content">
                <?php if ($query->have_posts()) : ?>
                    <?php while ($query->have_posts()) : $query->the_post(); ?>
                        <div id="tab-<?php echo get_the_ID(); ?>" class="tab-panel">
                            <p><?php the_content(); ?></p>
                            <?php
                            $topics_args = array(
                                'post_type' => 'topic',
                                'meta_query' => array(
                                    array(
                                        'key' => 'question_category',
                                        'value' => get_the_ID(),
                                        'compare' => '='
                                    )
                                )
                            );
                            $topics_query = new WP_Query($topics_args);
                            ?>
                            <?php if ($topics_query->have_posts()) : ?>

                                <div class="vertical-tabs">
                                    <?php if ($topics_query->have_posts()) : ?>
                                        <?php $first = true; ?>
                                        <div class="tab-buttons">
                                            <?php while ($topics_query->have_posts()) : $topics_query->the_post(); ?>
                                                <button class="tab-button<?php echo $first ? ' active' : ''; ?>" data-tab="tab-<?php the_ID(); ?>"><?php the_title(); ?></button>
                                                <?php $first = false; ?>
                                            <?php endwhile; ?>
                                        </div>
                                        <div class="tab-content">
                                            <?php while ($topics_query->have_posts()) : $topics_query->the_post(); ?>
                                                <div id="tab-<?php the_ID(); ?>" class="tab-panel">
                                                    <?php the_content(); ?>
                                                    <?php
                                                    $questions = get_post_meta(get_the_ID(), 'questions', true);
                                                    if ($questions && is_array($questions)) :
                                                        foreach ($questions as $question) :
                                                    ?>
                                                            <div class="question">
                                                                <strong><?php echo esc_html($question['type']); ?>: <?php echo esc_html($question['title']); ?></strong>
                                                                <p>How to Answer: <?php echo esc_html($question['how_to_answer']); ?></p>
                                                                <p>Example Answer: <?php echo wp_kses_post($question['example_answer']); ?></p>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endwhile; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php wp_reset_postdata(); ?>

                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                <?php endif; ?>
            </div>
        </div>
<?php
        return ob_get_clean();
    }


    /*   public function enqueue_scripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_style('question-bank-style', plugins_url('css/style.css', __FILE__));
        wp_enqueue_script('question-bank-script', plugins_url('js/script.js', __FILE__), array('jquery'), false, true);
    } */
}

$question_bank_plugin = new Question_Bank_Plugin();
$question_bank_plugin->run();
