<?php

class PgLookSqlException extends PgLookException
{
  protected $error_state;

  /**
   * __construct 
   * 
   * @param PDOStatement $stmt 
   * @param mixed $sql 
   * @access public
   * @return void
   */
  public function __construct(PDOStatement $stmt, $sql)
  {
    $this->error_state = $stmt->errorInfo();
    $this->message = sprintf("«%s».\n\nSQL error state '%s'\nextended status '%s'\n====\n%s\n====", $sql, $this->error_state[0], $this->error_state[1], $this->error_state[2]);
  }

  /**
   * getSQLErrorState 
   * Returns the SQLSTATE of the last SQL error
   * The list of SQLSTATEs is available at 
   * http://www.postgresql.org/docs/8.4/interactive/errcodes-appendix.html
   *
   * @access public
   * @return void
   */
  public function getSQLErrorState()
  {
    return $this->error_state[0];
  }

  /**
   * getSQLExtendedErrorStatus 
   * Returns the internal driver error code
   *
   * @access public
   * @return void
   */
  public function getSQLExtendedErrorStatus()
  {
    return $this->error_state[1];
  }

  /**
   * getSQLErrorMessage 
   * 
   * @access public
   * @return void
   */
  public function getSQLErrorMessage()
  {
    return $this->error_state[2];
  }
}
