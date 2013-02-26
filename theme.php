<?php
/**
 * Laravel Theme
 *
 * Build your Laravel views much easier with partials, layouts and themes
 * Inspired from Codeigniter Template Class by Philip Sturgeon (http://philsturgeon.co.uk/)
 *
 * @package         Laravel 3.2.X
 * @subpackage      Bundles
 * @category        LaravelTheme
 * @author          Raftalks
 * @license         http://opensource.org/licenses/gpl-3.0.html
 * @link            https://bitbucket.org/raftalks/laravel_theme/overview
 * @version         1.3 beta
 *
 *
 * // UPDATED CHANGES
 * Added method to support custom javascript to be added dynamically to the theme
 * Enabled partial views to access variables passed to main layout view without restriction.
 *
 */


class Theme {
    /**
     * Theme name;
     *
     * @var string
     */
    public $_theme_name;

    /**
     * Theme metadata set
     *
     * @var array
     */
    public $_theme_metadata = array();

    /**
     * Custom JS scripts
     *
     * @var array
     */
    public $_custom_js_scripts = array();

    /**
     * Theme dataset
     *
     * @var array
     */
    public $_theme_data = array();

    /**
     * Location : relative path
     *
     * @var string
     */
    public $_theme_path;

    /**
     * Set layout
     *
     * @var string
     */
    public $_layout;

    /**
     * Default layout
     *
     * @var string
     */
     public $_default_layout = "default";

    /**
     * Theme partials
     *
     * @var array
     */
    public $_theme_partials;

    /**
     * Config preferences of the theme
     * @var array
     */
    private $_config;

    /**
     * Page Title
     */
    private $_title;

    /**
     * create a new Theme object instance
     *
     * @param string    $theme
     * @param string    $config
     * @return void
     */
    public function __construct($theme_name, $config=NULL) {

        $this -> _theme_name = $theme_name;
        if($config !== NULL){
             $this->init($config);
        }

    }

    /**
     * Initialize the preferences
     *
     * @param array     $config
     * @return void
     */
    public function init($config = array()) {

        foreach ($config as $key => $val) {
            if ($key == 'theme' AND $val != '') {
                $this -> set_theme($val);
                continue;
            }

            $this -> {'_' . $key} = $val;
        }

    }

    /**
     * set Title of the page
     *
     * @param string    $title
     * @return void
     */
    public function title($title) {

        $this -> _title = $title;

        return $this;
    }

    /**
     * Sets the theme we want to use
     *
     * @param string    $theme
     * @param string    $path
     * @return void
     */
    public function set_theme($theme, $path = NULL) {

        $this -> _theme_name = $theme;

        if ($path !== NULL) {
            $this -> _theme_path = $path;
        }

        return $this;
    }

    /**
     * Set the theme layout to use
     *
     * @param string    $layout
     * @return void
     */
    public function set_layout($layout){

        $this->_layout = $layout;

        return $this;
    }


    /**
     * Register composer event to a view
     *
     * for registering a composer to a theme partial
     *
     * $theme->composer('menu', function($view){
     *       $view->with('theme_menu', "This is loaded from composer of the theme.");
     *  });
     *
     * @param  string|array  $view
     * @param  Closure       $composer
     * @return void
     */
    public function composer($views, $composer) {

        $views = (array)$views;

         $theme_name = $this->_theme_name;
         $base_path = path('public');
         $theme_path_relative = $this->_theme_path . '/' . $theme_name ;
         $theme_path_absolute = $base_path . $theme_path_relative;

         $theme_partials_path_absolute =  $theme_path_absolute . '/'. 'partials';

        foreach ($views as $view) {

                 if (file_exists($tpath = $theme_partials_path_absolute.'/'.$view. EXT)) {
                        $view = "path: " . $tpath; //View::make("path: " . $tpath);
                 } elseif (file_exists($tpath = $theme_partials_path_absolute.'/'.$view. BLADE_EXT)) {
                        $view = "path: " . $tpath; //View::make("path: " . $tpath);

                 }

            View::composer($view, $composer);

        }
    }

    /**
     * Sets theme partials of the theme
     *
     * @param string    $partial
     * @param string    $path
     */
    public function theme_partial($partial, $data = array()) {

        $this->_theme_partials['partials'][$partial] = $data;
        $this->_theme_data[$partial] = $data;
    }


    /**
     * Add a line to the end of the $theme['metadata'] string.
     */
    public function append_metadata($line){
        $this->_theme_metadata[] = $line;
    }

    /**
     * Add a line to the start of the $theme['metadata'] string.
     *
     * @param string $string
     */
    public function prepend_metadata($string){
        array_unshift($this->_theme_metadata, $string);
    }

    /**
     * Enables to embed custom js script into the theme and avoi'/' repetition
     * updated v1.3
     *
     * @param string #script
     * @param string $unique_key
     */
    public function add_js_script($script, $unique_key){

        $this->_custom_js_scripts[$unique_key] = $script;
    }


    public function add_asset($filename, $path = null) {

        $base_path = path('public'); // absolute path to public

        $theme_path_relative = $this->_theme_path;

        $theme = $this->_theme_name;


        //directory path set
        if ($path == NULL) {

            $directory = $theme_path_relative .'/'. $theme . '/assets/';
        } else {
            $directory = $path;
        }


        if(ends_with($filename, ".js")){

            $asset_file_path = $directory . "js/" . $filename;
        }

        if(ends_with($filename,".css")){

             $asset_file_path = $directory . "css/" . $filename;
        }

        if (isset($asset_file_path)) {

            //register the asset file into the template
            Asset::add($filename, $asset_file_path);
        }

    }

    /**
     * Render the theme
     *
     * @param string    $view
     * @param array     $data
     */
    public function render($page, $data = null) {

        if(is_array($data)){
            foreach($data as $kkey=>$ddata){
                $this->_theme_data[$kkey] = $ddata;
            }
        }


        $base_path = path('public'); // absolute path to public
        $theme_name = $this->_theme_name;

        $theme_path_relative = $this->_theme_path . '/' . $theme_name ;
        $theme_path_absolute = $base_path . $theme_path_relative;

        $useLayout = (!empty($this->_layout)) ? $this->_layout : $this->_default_layout;
        $LayoutView_path_absolute = $theme_path_absolute .'/'. "layouts" . '/' ;
        $LayoutFile_path_absolute = $LayoutView_path_absolute. $useLayout;

        if (file_exists($tpath = $LayoutFile_path_absolute . EXT)) {
            $view = View::make("path: " . $tpath);
        } elseif (file_exists($tpath = $LayoutFile_path_absolute . BLADE_EXT)) {
            $view = View::make("path: " . $tpath);

        }

        //stack process
        $this->_init_theme_func($theme_path_absolute);

        //include laravel assets within the theme
        $scripts = Asset::scripts();
        $styles = Asset::styles();

        $this->prepend_metadata($styles);
        $this->prepend_metadata($scripts);

        $theme['metadata'] = $this->_metadata();
        $theme['title'] = $this->_title;
        $theme['custom_js'] = $this->_custom_js();

        if (isset($view)) {


            View::share('theme_data', $theme); // share theme data to all the nested views

            $set_data =  $this->_theme_data;

            //nest view content
            //must auto load the partials in the directory
            //$view->nest('theme_footer', "path: " . $theme_path_absolute . "/partials/footer.php");
           // $view->nest('theme_header', "path: " . $theme_path_absolute. "/partials/header.php");

            $this->_load_partials($view);
            $view->nest('theme_content', $page, $set_data);


            if (!empty($set_data)) {
                foreach ($set_data as $key => $param) {
                    $view -> $key = $param;
                }
            }



            return $view;

        }
        else
        {
            die("Theme: ERROR - layout file $useLayout, was not available or is removed.");
           // return Response::error('404');
        }

    }


    /**
     * Return partial view rendered
     *
     * @param string $partial
     * @param array $data
     */
    public function render_partial($partial, $data = array()){


        $theme_partials = $this->_theme_partials['partials'];

         $theme_name = $this->_theme_name;
         $base_path = path('public');
         $theme_path_relative = $this->_theme_path . '/' . $theme_name ;
         $theme_path_absolute = $base_path . $theme_path_relative;

         $theme_p = array();
        if(!empty($theme_partials)){

            $theme_p = array_keys($theme_partials);
        }


        if(in_array($partial, $theme_p)){
            $set_data = $theme_partials[$partial];
            if(!empty($data)){
                if(!empty($set_data)){
                    $data = array_merge($set_data, $data);
                }
            }

            //try to load the partial of the theme


             if (file_exists($tpath = $theme_path_absolute .'/'. 'partials'.'/'.$partial. EXT)) {
                  return  View::make("path: " . $tpath, $data);
            } elseif (file_exists($tpath = $theme_path_absolute .'/'. 'partials'.'/'.$partial. BLADE_EXT)) {
                  return View::make("path: " . $tpath, $data);
            }


        }else{

            //try to load the partial view from laravel

            if(View::exists($partial)){
                return render($partial, $data);
            }else{
                //check if partial is available from theme
                 if (file_exists($tpath = $theme_path_absolute .'/'. 'partials'.'/'.$partial. EXT)) {
                  return  View::make("path: " . $tpath, $data);
                } elseif (file_exists($tpath = $theme_path_absolute .'/'. 'partials'.'/'.$partial. BLADE_EXT)) {
                      return View::make("path: " . $tpath, $data);
                }

            }


        }

        return FALSE;

    }

    /**
     * call theme_function
     *
     * @param string $path
     * @return void
     */
    private function _init_theme_func($path){


        //theme_function
        $theme_function = "theme_" . $this->_theme_name;


        //load theme_function file
        $theme_function_file = $path .'/'. 'theme_function.php';



        if (file_exists($theme_function_file)) {
            require_once($theme_function_file);

            if (function_exists($theme_function)) {

                call_user_func($theme_function, $this);
            }
        }
    }

    /**
     * return Custom JS scripts to be embeded
     * updated v1.3
     */
     private function _custom_js(){
         if(is_array($this->_custom_js_scripts)){
             $script = implode("\n",$this->_custom_js_scripts);

             $script = "<script type='text/javascript'>
                        $script
                        </script>";
             return $script;
         }

         return FALSE;
     }

    /**
     * return Metadata as string
     *
     * @return string
     */
     private function _metadata(){

         if(is_array($this->_theme_metadata)){

             $string = implode(" ", $this->_theme_metadata);
             return $string;
         }

         return FALSE;
     }

     /**
      * returns view object with nested partial views
      *
      * @param object $view_obj
      * @return void Object by reference
      */
     private function _load_partials(&$view_obj){

         if(isset($this->_theme_partials['partials']) && (is_array($this->_theme_partials['partials']))){
             if(!empty($this->_theme_partials['partials'])){

                 $partials = $this->_theme_partials['partials'];

                 $theme_name = $this->_theme_name;
                 $base_path = path('public');
                 $theme_path_relative = $this->_theme_path . '/' . $theme_name ;
                 $theme_path_absolute = $base_path . $theme_path_relative;

                 //updated below
                 foreach($partials as $partial => $pdata){
                     $tdata = $this->_theme_data; //updated v1.3
                     $data = array_merge($tdata, $pdata); //updated v1.3

                     $p_name = "theme_".$partial;
                     if(!empty($data)){

                           $view_obj->nest($p_name, "path: " . $theme_path_absolute . "/partials/$partial.php", $data);
                     }else{

                           $view_obj->nest($p_name, "path: " . $theme_path_absolute . "/partials/$partial.php");
                     }
                 }


             }
         }



     }

}

/**
 * Theme Helper Functions
 *
 */

 /**
  * Renders a partial of a theme or load a laravel view
  */
 function theme_partial($partial, $data=array()){

     $theme = IoC::resolve('Theme');


     return $theme->render_partial($partial, $data);

 }
