<?php

/**
 * Class Fast_Testimonial_Frontend
 */
class Fast_Testimonial_Frontend
{

    private static $instance = null;
    public static function get_instance() {
        if ( ! self::$instance)
            self::$instance = new self();
        return self::$instance;
    }

    /**
     * Initialize global hooks.
     */
    public function init() {

        // Load shortcode page.
        add_shortcode('fast-testimonial', array( $this, 'fast_testminial_load_shortcode_callback' ));

        // Load style and script. 
        add_action('wp_enqueue_scripts', array( $this, 'fast_testimonial_frontend_script_callback' ));
        // Load Ajax. 
        add_action('wp_ajax_fast_ajax_searchresult', array( $this, 'fast_ajax_searchresult' ));
        add_action('wp_ajax_nopriv_fast_ajax_searchresult', array( $this, 'fast_ajax_searchresult' ));
    }


    public function fast_testminial_load_shortcode_callback() {


        ob_start(); 
        
        ?>

        <div class="fastajax-blog-filter">
            <div class="fastajax-searchbox">
                <form action="" id="fastajax-searchboxform">
                    <input type="text" class="fastinput" data-nonce="<?php echo wp_create_nonce('fast_ajax_nonce'); ?>" id="fastinput" placeholder="Search Here..." >
                    <button>Search</button>
                </form>
                
            </div>
            <div class="fastajax-catfilter">
                <div class="single-fitler">
                    <label for="">Select Category</label>
                    <select class="fast-select" id="fast_cat">
                        <option value="cat1">Category One</option>
                        <option value="cat2">Category Two</option>
                        <option value="cat3">Category Three</option>
                    </select>
                </div>
                <div class="single-fitler">
                    <label for="">Select Author</label>
                    <select class="fast-select" id="fast_author">
                        <option value="cat1">Author One</option>
                        <option value="cat2">Author Two</option>
                    </select>
                </div>
            </div>

            <div class="filterOutputs" id="filterOutputs">
                <div class="searchOutput" id="searchOutput">

                </div>
            </div>


        </div>

        <?php $allcontents = ob_get_contents(); ?>
        <?php ob_get_clean();
        return $allcontents;
    }

    function fast_ajax_searchresult(){

        if(wp_verify_nonce($_POST['searchNonce'], 'fast_ajax_nonce')) :

        if(isset($_POST['search_data']) && !empty($_POST['search_data'])) : ?>
        <div class="ajax-searchloading"></div>
        <div class="upk-post-grid upk-style-1">

            <?php
                $ajax_search_query = new Wp_Query(array(
                    'post_type' => 'post',
                    'posts_per_page' => 12,
                    's' => $_POST['search_data'],
                ));

                if($ajax_search_query->have_posts()) : while($ajax_search_query->have_posts()) : $ajax_search_query->the_post();

            ?>
    
                    <div class="upk-item">
                        <div class="upk-item-box">
                            <div class="upk-img-wrap">
                                <div class="upk-main-img">
                                <?php echo the_post_thumbnail(); ?>
                                </div>
                            </div>
                            <div class="upk-content">
                                <div>
                                <h3 class="upk-title"><a href="<?php the_permalink(); ?>" title="<?php echo the_title(); ?>" class="title-animation-underline"><?php echo the_title(); ?></a></h3>
                                <div class="upk-text-wrap">
                                    <div class="upk-text">
                                        <p><?php echo wp_trim_words( get_the_content(), 15, '...' ); ?></p>
                                    </div>
                                </div>
                                <div class="upk-meta">
                                    <div class="upk-blog-author" data-separator="|">
                                        <a class="author-name" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a>
                                    </div>
                                    <div data-separator="|">
                                        <div class="upk-date">
                                            <?php echo get_the_date();?>		
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; 

                else:  ?>

                <p style="color: red">Sorry Not Found for the word <strong><?php echo esc_html($_POST['search_data']) ; ?></strong></p>
               
                <?php endif; ?>

        <?php 
        endif; endif; exit;

    }

    /**
     * Load frontend style and script.
     *
     * @return void
     */
    function fast_testimonial_frontend_script_callback() {
        // CSS
        wp_enqueue_style('fast-testimonial_slick', plugin_dir_url(__DIR__) . 'assets/css/blogfilter.css', array(), AJAX_FASTPOST_FILTER_VERSION);

        // JS
        wp_enqueue_script('jquery');
        wp_enqueue_script('fast-ajax-search', plugin_dir_url(__DIR__) . 'assets/js/fast-ajax-search.js', array( 'jquery' ), AJAX_FASTPOST_FILTER_VERSION, true);
        wp_localize_script('fast-ajax-search', 'fastAjaxSearch', array('ajaxurl' => admin_url('admin-ajax.php')));
        
    }
}

Fast_Testimonial_Frontend::get_instance()->init();
