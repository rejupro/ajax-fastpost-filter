<?php

/**
 * Class Fast_Testimonial_Loader
 */
class Fast_Testimonial_Loader{

    // Autoload dependency.
    public function __construct(){
        $this->load_dependency();
    }

    /**
     * Load all Plugin FIle.
     */
    public function load_dependency(){
        include_once(dirname( __FILE__ ). '/../frontend/Fast_Testimonial_Frontend.php');
    }
}


/**
 * Initialize load class .
 */
function fast_testimonial_run(){
    if ( class_exists( 'Fast_Testimonial_Loader' ) ) {
        new Fast_Testimonial_Loader();
    }
}
