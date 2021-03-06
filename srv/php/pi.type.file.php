<?

  /**
   *  Pi Type File class
   *
   * @author 2011-2014 Johan Telstad <jt@viewshq.no>
   */



  require_once('pi.type.permissions.php');

  // // borrow pattern from posix
  // define('POSIX_STICKY', 01000);
  // define('POSIX_SETGID', 02000);
  // define('POSIX_SETUID', 04000);

  // define('POSIX_ALL', 0777);

  // // defines the bit positions of permissions (i.e. how many right shifts we 
  // // need to use to read it from the bitfield) 
  // define('POSIX_USER',  6);
  // define('POSIX_OWNER', 6);
  // define('POSIX_GROUP', 3);


  // define('POSIX_X', 1);
  // define('POSIX_W', 2);
  // define('POSIX_R', 4);

  // define('POSIX_RX',  5);
  // define('POSIX_RW',  6);
  // define('POSIX_RWX', 7);

  // define('POSIX_READ',  POSIX_R);
  // define('POSIX_WRITE', POSIX_W);
  // define('POSIX_EXEC',  POSIX_X);



  class PiTypeFile extends PiType {

    protected $name   = 'file';
    protected $TYPE   = PiType::FILE;

    protected $SIZE   = null;

    protected $filename = "";
    protected $exists   = null;

    protected $BITS   = 8;

    protected $permissions  = new PiTypePermissions();

    protected $value = 0;

    public function __construct($value = 0, $ttl = null, $size = 0) {

      if ($size && is_int($size)) {
        $this->SIZE = $size;
      }

      if (is_string($value)) {
        $this->value  = $value;
        $this->exists = file_exists($value);
      }

      if ($this->exists) {
        $this->SIZE = filesize($this->value);
      }

      // call PiType class constructor (pass along arguments)
      // parent::__construct($this->TYPE, $ttl);
    }


    public function setAll($value) {
      $this->value = $value & 07777; // mask is to prevent overflow of internal value
    }


    // $file = 'some_file';

    // if (posix_access($file, POSIX_R_OK | POSIX_W_OK)) {
    //     echo 'The file is readable and writable!';

    // } else {
    //     $error = posix_get_last_error();

    //     echo "Error $error: " . posix_strerror($error);
    // }




    /**
     * Get permissions for owner
     * @return octal Permission mask for owner
     */
    public function getOwnerPermissions() {
      return ($this->value >> POSIX_OWNER) & 7; // mask is to prevent overflow
    }

    /**
     * Get permissions for group
     * @return octal Permission mask for group
     */
    public function getGroupPermissions() {
      return ($this->value >> POSIX_GROUP) & 7;
    }

    /**
     * Get permissions for all users
     * @return octal Permission mask for all users
     */
    public function getPermissions() {
      return $this->value & 7;
    }


    /**
     * Set permissions for owner
     * @param octal   $value  Permission mask for owner
     */
    public function setOwnerPermissions($value = POSIX_RWX) {
      $this->value |= (($value & 7) << POSIX_OWNER);
    }

    /**
     * Set permissions for group
     * @param octal   $value  Permission mask for group
     */
    public function setGroupPermissions($value = POSIX_RX) {
      $this->value |= (($value & 7) << POSIX_GROUP);
    }

    /**
     * Set permissions for all users
     * @param octal   $value  Permission mask for all users
     */
    public function setPermissions($value = POSIX_RX) {
      $this->value |= ($value & 7);
    }


    /**
     * Get the sticky flag
     * @return bool Whether the sticky bit is set or not
     */
    public function getSticky() {
      return $this->value & POSIX_STICKY; 
    }

    /**
     * Get the setuid flag
     * @return bool Whether the setuid bit is set or not
     */
    public function getSetUID() {
      return $this->value & POSIX_SETUID; 
    }

    /**
     * Get the setgid flag
     * @return bool Whether the setgid bit is set or not
     */
    public function getSetGID() {
      return $this->value & POSIX_SETGID; 
    }


    /**
     * Set the sticky flag
     */
    public function setSticky() {
      $this->value |= POSIX_STICKY; 
    }

    /**
     * Set the setuid flag
     */
    public function setSetUID() {
      $this->value |= POSIX_SETUID; 
    }

    /**
     * Set the setgid flag
     */
    public function setSetGID() {
      $this->value |= POSIX_SETGID; 
    }

    /**
     * Switch off the sticky flag
     */
    public function unsetSticky() {
      $this->value &= (~POSIX_STICKY); 
    }

    /**
     * Switch off the setuid flag
     */
    public function unsetSetUID() {
      $this->value &= (~POSIX_SETUID); 
    }

    /**
     * Switch off the setgid flag
     */
    public function unsetSetGID() {
      $this->value &= (~POSIX_SETGID); 
    }


    /**
     * Flip the sticky flag
     * @return int New value of permissions
     */
    public function flipSticky() {
      return $this->value ^= POSIX_STICKY; 
    }

    /**
     * Flip the setuid flag
     * @return int New value of permissions
     */
    public function flipSetUID() {
      return $this->value ^= POSIX_SETUID; 
    }

    /**
     * Flip the setgid flag
     * @return int New value of permissions
     */
    public function flipSetGID() {
      return $this->value ^= POSIX_SETGID; 
    }


    public function get () {
      return $this->value;
    }

    public function set ($value = null) {
      $this->value = $value;
    }

    public function getset ($value = null) {
      $previous = $this->value;
      $this->value = $value;
      return $previous;
    }

  }



?>