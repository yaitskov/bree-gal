<?php


class WebUser extends CWebUser {
  private $_user_model = null;

  public function getType() {
      return $this->getState(UserIdentity::USER_TYPE);
  }
  /**
   *  label user type of current one.
   */
  public function getLabel() {
      $ptype = $this->getType();
      return User::getAccountTypeLabel($ptype);
  }

  /**
   * Returns user entity related with current user.
   */
  public function getModel() {
    if (!$this->_user_model && $this->id)
      $this->_user_model = User::model()->findByPk($this->id);
    return $this->_user_model ;
  }

  public function login($identity, $duration = 0) {
    parent::login($identity, $duration);
    // $this->userModel->logined();
  }

  public function logout($destroySession = true) {
      //if ($this->userModel)
      // $this->userModel->logouted();
    parent::logout();
  }

  protected function renewCookie(){
      //if ($this->Model)
      // $this->userModel->acted();
    parent::renewCookie();
  }

  public function getIsModerator() {
      return $this->type == User::AT_MODERATOR or $this->isGod;
  }

  public function getIsTrial() {
      return $this->type == User::TRIAL;
  }

  public function getIsGod() {
      return $this->type == User::AT_ADMIN or $this->isDeveloper;
  }

  public function getIsDeveloper() {
      return $this->type == USER::AT_DEVELOPER;
  }

  public function getIsMereMortal() {
      return $this->type == User::TRIAL
          or $this->type == User::AT_BUSINESS
          or $this->isGod;
  }

  public function getIconUrl() {
      return Yii::app()->session['avatar-icon'];
  }

  public function getIsFaceBook() {
      return $this->getState('user-flags') & User::FACEBOOK;
  }

  /* public function setReturnUrl( $url ) { */
  /*   if ( preg_match ( '/upload/', $url ) ) { */
  /*     throw new Exception ( "zzzzzzzzzzzzzz" . $url ) ; */
  /*   } */
  /*   Return parent::setReturnUrl( $url ) ; */
  /* } */
}

?>