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

        // Load Post Meta
        add_action('add_meta_boxes', array( $this, 'add_meta_box' ));
        add_action('save_post',      array( $this, 'save' ));

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
        // Load Post Publisher 
        add_action('wp_ajax_fast_ajax_postpublisher', array( $this, 'fast_ajax_postpublisher' ));
        add_action('wp_ajax_nopriv_fast_ajax_postpublisher', array( $this, 'fast_ajax_postpublisher' ));
        // Load Post Year 
        add_action('wp_ajax_fast_ajax_fastYear', array( $this, 'fast_ajax_fastYear' ));
        add_action('wp_ajax_nopriv_fast_ajax_fastYear', array( $this, 'fast_ajax_fastYear' ));
        // Load Post Month 
        add_action('wp_ajax_fast_ajax_ajaxMonthin', array( $this, 'fast_ajax_ajaxMonthin' ));
        add_action('wp_ajax_nopriv_fast_ajax_ajaxMonthin', array( $this, 'fast_ajax_ajaxMonthin' ));
    }


    // Metaboxes Options
    /**
     * Adds the meta box container.
     */
    public function add_meta_box( $post_type ) {
        // Limit meta box to certain post types.
        $post_types = array( 'post' );

        if ( in_array($post_type, $post_types) ) {
            add_meta_box(
                'some_meta_box_name',
                __('Publication URL & Publisher Option', 'ajax-fast-publication-post'),
                array( $this, 'render_meta_box_content' ),
                $post_type,
                'advanced',
                'high'
            );
        }
    }

    /**
     * Save the meta when the post is saved.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function save( $post_id ) {

        /*
	         * We need to verify this came from the our screen and with proper authorization,
	         * because save_post can be triggered at other times.
	         */

        // Check if our nonce is set.
        if ( ! isset($_POST['fasttestimonial_inner_custom_box_nonce']) ) {
            return $post_id;
        }

        $nonce = sanitize_text_field(wp_unslash($_POST['fasttestimonial_inner_custom_box_nonce'] ?? ''));

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce($nonce, 'fasttestimonial_inner_custom_box') ) {
            return $post_id;
        }

        /*
	         * If this is an autosave, our form has not been submitted,
	         * so we don't want to do anything.
	         */
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
            return $post_id;
        }

        /* OK, it's safe for us to save the data now. */

        // Sanitize the user input.
        $publication_publisher = sanitize_text_field(wp_unslash($_POST['fsmpost_publisher'] ?? ''));

        // Update the meta field
        update_post_meta($post_id, '_fsmpost_publisher', $publication_publisher);
    }


    /**
     * Render Meta Box content.
     *
     * @param WP_Post $post The post object.
     */
    public function render_meta_box_content( $post ) {

        // Add an nonce field so we can check for it later.
        wp_nonce_field('fasttestimonial_inner_custom_box', 'fasttestimonial_inner_custom_box_nonce');

        // Use get_post_meta to retrieve an existing value from the database.
        $publication_publisher = get_post_meta($post->ID, '_fsmpost_publisher', true);

        // Display the form, using the current value.
        ?>
        <label for="publication_publisher">
            <?php esc_html_e('Select Publisher', 'fast-testimonial'); ?>
        </label>

        <select class="widefat" name="fsmpost_publisher">
            <option value="" selected="" disabled>Select Publisher</option>
            <?php
            global $wpdb;
            $table = $wpdb->prefix.'fastpublication_publisher';
            $datas = $wpdb->get_results ( "SELECT * FROM $table ORDER BY id DESC");
            foreach($datas as $single) :
            ?>
            <option <?php if ( $single->id == $publication_publisher ) echo "selected='selected'"; ?> value="<?php echo $single->id?>"><?php echo $single->name . ' - ' . $single->email ; ?></option>
            <?php endforeach; ?>
        </select>

        <?php
    }



    public function fast_testminial_load_shortcode_callback() {

        ob_start(); 
        
        ?>

        <div class="fastajax-blog-filter">
            <div class="fastajax-searchbox">
                <form action="" id="fastajax-searchboxform" autocomplete="off">
                    <input type="text" class="fastinput" data-nonce="<?php echo wp_create_nonce('fast_ajax_nonce'); ?>" id="fastinput" placeholder="Search Here..." >
                    <button>Search</button>
                </form>
                
            </div>
            <div class="fastajax-catfilter">

                <?php
                   function get_posts_years_array() {
                        global $wpdb;
                        $result = array();
                        $years = $wpdb->get_results(
                            $wpdb->prepare(
                                "SELECT YEAR(post_date) FROM {$wpdb->posts} WHERE post_status = 'publish' GROUP BY YEAR(post_date) DESC"
                            ),
                            ARRAY_N
                        );
                        if ( is_array( $years ) && count( $years ) > 0 ) {
                            foreach ( $years as $year ) {
                                $result[] = $year[0];
                            }
                        }
                        return $result;
                    }
                    
                    // Echo the years out wherever you want
                    $years = get_posts_years_array();
                ?>

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
                <div class="single-fitler">
                    <label>Select Publisher</label>
                    <select class="fast-select" id="fast_postpublisher">
                        <option value="" selected="" disabled>Select Publisher</option>
                        <?php
                        global $wpdb;
                        $table = $wpdb->prefix.'fastpublication_publisher';
                        $datas = $wpdb->get_results ( "SELECT * FROM $table ORDER BY id DESC");
                        foreach($datas as $single) :
                        ?>
                        <option value="<?php echo $single->id; ?>"><?php echo $single->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="single-fitler">
                    <label for="">Select Year</label>
                    <select class="fast-select" id="fast_year">
                        <option value="" selected="" disabled>Select Year</option>
                        <?php foreach($years as $single) : ?>
                        <option value="<?php echo $single; ?>"><?php echo $single; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="single-fitler month_filter">
                    <label for="">Select Month</label>
                    <select class="fast-select" id="fast_month">
                        <option value="" selected="" disabled>Select Month</option>
                        <option value="1">January</option>
                        <option value="2">February</option>
                        <option value="3">March </option>
                        <option value="4">April</option>
                        <option value="5">May</option>
                        <option value="6">June</option>
                        <option value="7">July</option>
                        <option value="8">August</option>
                        <option value="9">September </option>
                        <option value="10">October </option>
                        <option value="11">November</option>
                        <option value="12">December </option>
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
                    'posts_per_page' => 20,
                    'post_status' => 'publish',
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
                        'posts_per_page' => 20,
                        'post_status' => 'publish',
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
                        'posts_per_page' => 20,
                        'post_status' => 'publish',
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
    

    // Post by Publisher
    function fast_ajax_postpublisher(){
        if(isset($_POST['publisherin'])) : ?>
            <div class="ajax-searchloading"></div>
            <div class="fastajax-post-grid">
    
                <?php
                    $ajax_search_query = new Wp_Query(array(
                        'post_type' => 'post',
                        'posts_per_page' => 20,
                        'post_status' => 'publish',
                        'meta_key' => '_fsmpost_publisher',
                        'meta_value' => $_POST['publisherin']
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
    
                    <p style="color: red">Sorry Not Found any post from this Publisher</strong></p>
                   
                    <?php endif; ?>
    
            <?php 
            endif; exit;
    }


    // Post by Year
    
    function fast_ajax_fastYear(){

        if(isset($_POST['fastYear'])) : ?>
            <div class="ajax-searchloading"></div>
            <div class="fastajax-post-grid">
    
                <?php
                    $ajax_search_query = new Wp_Query(array(
                        'post_type' => 'post',
                        'posts_per_page' => 20,
                        'post_status' => 'publish',
                        'date_query' => array(
                            'year' => $_POST['fastYear'],
                        ),
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
    
                    <p style="color: red">Sorry Not Found any post from this Year <strong><?php echo $_POST['yearin'] ; ?></strong></p>
                   
                    <?php endif; ?>
    
        <?php 
        endif; exit;
    }
    
    // Post by Month
    function fast_ajax_ajaxMonthin(){
        
        if(isset($_POST['fastMonthin'])) : ?>
            <div class="ajax-searchloading"></div>

            <?php
                if($_POST['fastMonthin'] == 1){
                    $monthname = 'January';
                }elseif($_POST['fastMonthin'] == 2){
                    $monthname = 'February';
                }elseif($_POST['fastMonthin'] == 3){
                    $monthname = 'March';
                }elseif($_POST['fastMonthin'] == 4){
                    $monthname = 'April';
                }elseif($_POST['fastMonthin'] == 5){
                    $monthname = 'May';
                }elseif($_POST['fastMonthin'] == 6){
                    $monthname = 'June';
                }elseif($_POST['fastMonthin'] == 7){
                    $monthname = 'July';
                }elseif($_POST['fastMonthin'] == 8){
                    $monthname = 'August';
                }elseif($_POST['fastMonthin'] == 9){
                    $monthname = 'September';
                }elseif($_POST['fastMonthin'] == 10){
                    $monthname = 'October';
                }elseif($_POST['fastMonthin'] == 11){
                    $monthname = 'November';
                }else{
                    $monthname = 'December';
                }
            ?>
            
            <p style="color: green; width: 100%; display: block; margin-bottom: 5px;">Result For Year <strong><?php echo $_POST['fastYearin'] ?></strong> Month <strong><?php echo $monthname ; ?></strong></p>
            <div class="fastajax-post-grid">
                
                <?php
                    $ajax_search_query = new Wp_Query(array(
                        'post_type' => 'post',
                        'posts_per_page' => 20,
                        'post_status' => 'publish',
                        'date_query' => array(
                            
                            'year' => $_POST['fastYearin'],
                            'month' => $_POST['fastMonthin'],
                            
                        ),
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
    
                    <p style="color: red">Sorry Not Found any post from this Year <strong><?php echo $_POST['fastYearin'] ; ?> and month</strong></p>
                   
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
        wp_localize_script('fast-ajax-search', 'fastAjaxpostpublisher', array('ajaxurl' => admin_url('admin-ajax.php')));
        wp_localize_script('fast-ajax-search', 'fastAjaxyear', array('ajaxurl' => admin_url('admin-ajax.php')));
        wp_localize_script('fast-ajax-search', 'fastAjaxmonth', array('ajaxurl' => admin_url('admin-ajax.php')));
        
    }
}

Fast_Testimonial_Frontend::get_instance()->init();
