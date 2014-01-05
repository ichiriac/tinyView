<?php
namespace tiny;
/**
 * A minimalistic view layer implementation based on phtml
 * @author Ioan CHIRIAC
 * @license MIT
 */
class View {
    /**
     * Configure the directory that contains layouts
     */
    protected $layoutPath = './layout';
    /**
     * Configure the directory that contains views
     */
    protected $viewsPath = './views';
    /**
     * The default files extension
     */
    protected $ext = '.phtml';
    // contains the current selected layout
    private $layout = 'default';
    // contains the target to the main view file
    private $content;
    // contains data passed on views
    private $data = array();
    /**
     * Initialize a new view renderer
     */
    final public function __construct($context, $layout = null) {
        if (!is_null($layout)) $this->layout = $layout;
        // creating default vars on include scope
        $this->data['context'] = $context;
        $this->data['view'] = $this;
    }
    /**
     * Configure path
     */
    final public function path($views, $layout = null, $ext = null) {
        $this->viewsPath = rtrim($views, '\\/');
        if (!empty($layout)) $this->layoutPath = rtrim($layout, '\\/');
        if (!empty($ext)) $this->ext = $ext;
        return $this;
    }
    /**
     * Inserts a path to check before others
     */
    final public function insertPath($views, $layout = null) {
        if (!is_array($this->viewsPath)) $this->viewsPath = array($this->viewsPath);
        array_unshift($this->viewsPath, rtrim($views, '\\/'));
        if (!empty($layout)) {
            if (!is_array($this->layoutPath)) $this->layoutPath = array($this->layoutPath);
            array_unshift($this->layoutPath, rtrim($layout, '\\/'));
        }
        return $this;
    }
    /**
     * Responds with the specified view
     */
    final public function respond($content, array $data = null) {
        if (!empty($data)) $this->set($data);
        $this->content = $content;
        return $this;
    }
    /**
     * Sets some pair of keys / values
     *      
     */
    final public function set($key, $value = null) {
        if (is_array($key)) {
            foreach($key as $k => $v) $this->data[$k] = $v;
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }
    /**
     * Sets a new layout
     */
    final public function setLayout($layout) {
        $this->layout = $layout;
        return $this;
    }
    /**
     * Locates the specified file into the specified path
     * @return string|boolean Returns the full path to the file if found, or false
     */
    protected function locate($path, $file) {
        if (is_array($path)) {
            foreach($path as $p) {
                if ($target = $this->locate($p, $file)) {
                    return $target;
                }
            }
        } else {
            $target = $path . DIRECTORY_SEPARATOR . ltrim($file, '\\/') . $this->ext;
            if (file_exists($target)) return $target;
        }
        return false;
    }
    /**
     * Renders the specified view including specified data
     */
    public function render( $_view, $_data = null ) {
        $_target = $this->locate( $this->viewsPath, $_view );
        if ($_target !== false) {
            extract($this->data);
            if (!empty($_data)) extract($_data);
            ob_start();
            include $_target;
            return ob_get_clean();
        } else {
            return false;
        }
    }
    /**
     * Outputs the current layout including it's main content
     */
    public function output() {
        $content = $this->render($this->content);
        if ( $content === false ) {
            throw new ViewNotFound(
                sprintf(
                    'Unable to locate the view "%1$s"'
                    , $this->content
                )
            );
        }
        if (!empty($this->layout)) {
            $_target = $this->locate( $this->layoutPath, $this->layout );;
            if ($_target!==false) {
                extract($this->data, EXTR_SKIP);
                ob_start();
                include $_target;
                return ob_get_clean();
            } else {
                throw new ViewNotFound(
                    sprintf(
                        'Unable to locate the layout "%1$s"'
                        , $this->layout
                    )
                );
            }
        } else {
            return $content;
        }
    }
    /**
     * Converts the current view as a string by rendering its layout and content
     */
    public function __toString() {
        try {
            return $this->output();
        } catch(ViewNotFound $ex) {
            return 'Error : ' . $ex->getMessage();
        }
    }
}
/**
 * Occurs when a view is not found
 */
class ViewNotFound extends \Exception { }