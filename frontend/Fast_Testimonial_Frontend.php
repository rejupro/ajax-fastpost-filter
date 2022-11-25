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
        // Load Post Year 
        add_action('wp_ajax_fast_ajax_fastYear', array( $this, 'fast_ajax_fastYear' ));
        add_action('wp_ajax_nopriv_fast_ajax_fastYear', array( $this, 'fast_ajax_fastYear' ));
        // Load Post Month 
        add_action('wp_ajax_fast_ajax_ajaxMonthin', array( $this, 'fast_ajax_ajaxMonthin' ));
        add_action('wp_ajax_nopriv_fast_ajax_ajaxMonthin', array( $this, 'fast_ajax_ajaxMonthin' ));
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
    
    // Post by Year
    function fast_ajax_fastYear(){

        if(isset($_POST['fastYear'])) : ?>
            <div class="ajax-searchloading"></div>
            <div class="fastajax-post-grid">
    
                <?php
                    $ajax_search_query = new Wp_Query(array(
                        'post_type' => 'post',
                        'posts_per_page' => 12,
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
                        'posts_per_page' => 12,
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
    
                    <p style="color: red">Sorry Not Found any post from this Year <strong><?php echo $_POST['yearin'] ; ?></strong></p>
                   
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
        wp_localize_script('fast-ajax-search', 'fastAjaxyear', array('ajaxurl' => admin_url('admin-ajax.php')));
        wp_localize_script('fast-ajax-search', 'fastAjaxmonth', array('ajaxurl' => admin_url('admin-ajax.php')));
        
    }
}

Fast_Testimonial_Frontend::get_instance()->init();
