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
        // Load Post Category 
        add_action('wp_ajax_fast_ajax_catin', array( $this, 'fast_ajax_catin' ));
        add_action('wp_ajax_nopriv_fast_ajax_catin', array( $this, 'fast_ajax_catin' ));
        // Load Post Author 
        add_action('wp_ajax_fast_ajax_authorin', array( $this, 'fast_ajax_authorin' ));
        add_action('wp_ajax_nopriv_fast_ajax_authorin', array( $this, 'fast_ajax_authorin' ));
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
                    <?php
                        $categories = get_categories( array(
                            'orderby' => 'name',
                            'order'   => 'ASC'
                        ) );
                    ?>
                    <select class="fast-select" id="fast_cat">
                        <option value="" selected="" disabled>Select Category</option>
                        <?php foreach($categories as $single) : ?>
                        <option value="<?php echo $single->term_id; ?>"><?php echo $single->name; ?></option>
                        <?php endforeach ; ?>
                    </select>
                </div>
                <div class="single-fitler">
                    <label for="">Select Author</label>
                    <select class="fast-select" id="fast_author">
                        <option value="" selected="" disabled>Select Author</option>
                        <?php 
                            $users = get_users();
                        ?>
                        <?php foreach($users as $single) : ?>
                        <option value="<?php echo $single->id; ?>"><?php echo $single->display_name; ?></option>
                        <?php endforeach; ?>
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

    // Search Result
    function fast_ajax_searchresult(){
        if(wp_verify_nonce($_POST['searchNonce'], 'fast_ajax_nonce')) :
        if(isset($_POST['search_data']) && !empty($_POST['search_data'])) : ?>
        <div class="ajax-searchloading"></div>
        <div class="fastajax-post-grid">

            <?php
                $ajax_search_query = new Wp_Query(array(
                    'post_type' => 'post',
                    'posts_per_page' => 12,
                    's' => $_POST['search_data'],
                ));

                if($ajax_search_query->have_posts()) : while($ajax_search_query->have_posts()) : $ajax_search_query->the_post();

            ?>
    
                    <div class="fastajax-item">
                        <div class="fastajax-item-box">
                            <div class="fastajax-img-wrap">
                                <div class="fastajax-main-img">
                                <?php echo the_post_thumbnail(); ?>
                                </div>
                            </div>
                            <div class="fastajax-content">
                                <div>
                                <h3 class="fastajax-title"><a href="<?php the_permalink(); ?>"><?php echo the_title(); ?></a></h3>
                                <div class="fastajax-text-wrap">
                                    <div class="fastajax-text">
                                        <p><?php echo wp_trim_words( get_the_content(), 15, '...' ); ?></p>
                                    </div>
                                </div>
                                <div class="fastajax-meta">
                                    <div class="fastajax-blog-author" data-separator="|">
                                        <a class="author-name" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a>
                                    </div>
                                    <div data-separator="|">
                                        <div class="fastajax-date">
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
    // Post by Category
    function fast_ajax_catin(){
        
        if(isset($_POST['catin'])) : ?>
            <div class="ajax-searchloading"></div>
            <div class="fastajax-post-grid">
    
                <?php
                    $ajax_search_query = new Wp_Query(array(
                        'post_type' => 'post',
                        'posts_per_page' => 12,
                        'category__in' => $_POST['catin']
                    ));
    
                    if($ajax_search_query->have_posts()) : while($ajax_search_query->have_posts()) : $ajax_search_query->the_post();
    
                ?>
        
                        <div class="fastajax-item">
                            <div class="fastajax-item-box">
                                <div class="fastajax-img-wrap">
                                    <div class="fastajax-main-img">
                                    <?php echo the_post_thumbnail(); ?>
                                    </div>
                                </div>
                                <div class="fastajax-content">
                                    <div>
                                    <h3 class="fastajax-title"><a href="<?php the_permalink(); ?>"><?php echo the_title(); ?></a></h3>
                                    <div class="fastajax-text-wrap">
                                        <div class="fastajax-text">
                                            <p><?php echo wp_trim_words( get_the_content(), 15, '...' ); ?></p>
                                        </div>
                                    </div>
                                    <div class="fastajax-meta">
                                        <div class="fastajax-blog-author" data-separator="|">
                                            <a class="author-name" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a>
                                        </div>
                                        <div data-separator="|">
                                            <div class="fastajax-date">
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
    
                    <p style="color: red">Sorry Not Found any post from this category</p>
                   
                    <?php endif; ?>
    
            <?php 
            endif; exit;
    }
    // Post by Author
    function fast_ajax_authorin(){
        
        if(isset($_POST['authorin'])) : ?>
            <div class="ajax-searchloading"></div>
            <div class="fastajax-post-grid">
    
                <?php
                    $ajax_search_query = new Wp_Query(array(
                        'post_type' => 'post',
                        'posts_per_page' => 12,
                        'author' => $_POST['authorin']
                    ));
    
                    if($ajax_search_query->have_posts()) : while($ajax_search_query->have_posts()) : $ajax_search_query->the_post();
    
                ?>
        
                        <div class="fastajax-item">
                            <div class="fastajax-item-box">
                                <div class="fastajax-img-wrap">
                                    <div class="fastajax-main-img">
                                    <?php echo the_post_thumbnail(); ?>
                                    </div>
                                </div>
                                <div class="fastajax-content">
                                    <div>
                                    <h3 class="fastajax-title"><a href="<?php the_permalink(); ?>"><?php echo the_title(); ?></a></h3>
                                    <div class="fastajax-text-wrap">
                                        <div class="fastajax-text">
                                            <p><?php echo wp_trim_words( get_the_content(), 15, '...' ); ?></p>
                                        </div>
                                    </div>
                                    <div class="fastajax-meta">
                                        <div class="fastajax-blog-author" data-separator="|">
                                            <a class="author-name" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a>
                                        </div>
                                        <div data-separator="|">
                                            <div class="fastajax-date">
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
    
                    <p style="color: red">Sorry Not Found any post from this user</strong></p>
                   
                    <?php endif; ?>
    
            <?php 
            endif; exit;
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
        wp_localize_script('fast-ajax-search', 'fastAjaxcategory', array('ajaxurl' => admin_url('admin-ajax.php')));
        wp_localize_script('fast-ajax-search', 'fastAjaxauthor', array('ajaxurl' => admin_url('admin-ajax.php')));
        
    }
}

Fast_Testimonial_Frontend::get_instance()->init();
