<?php
  require_once('db_connect.php');

  class database_connect
  {
    public $_con = null;

    function __construct()
    {
      $this->connect();
    }

    function __destruct()
    {
      $this->close();
    }

    function query($_query)
    {
      if ($this->_con == null)
        $this->connect();
      return mysqli_query($this->_con, $_query);
    }

    function lastId()
    {
      return mysqli_insert_id($this->_con);
    }

    function isConnected()
    {
      return ($this->_con !== null);
    }

    function connect()
    {
      if (!$this->_con)
      {
        require_once('db_config.php');
        $this->_con = mysqli_connect(database_url, database_user, database_password, database_name);
      }
      if (!$this->_con)
      {
        die("Connection failure to database ... ".mysqli_connect_error());
      }
    }

    function close()
    {
      if ($this->_con)
      {
        $this->_con->close();
        $this->_con = null;
      }
    }
  }

?>
