<?php if (!defined('APPLICATION')) exit();
/*
Plugin Name: uLogin - виджет авторизации через социальные сети
Plugin URI: http://ulogin.ru/
Description: uLogin
Version: 1.6
Author: uLogin
Author URI: http://ulogin.ru/
License: GPL3
*/

// Define the plugin:
$PluginInfo['uLogin'] = array(
  'Name' => 'uLogin Plugin',
  'Description' => 'This plugin allows users to sign in with their Facebook, Vkontakte, Odnoklassniki, Google accounts.',
  'Version' => '1.0',
  'RequiredTheme' => FALSE,
  'MobileFriendly' => TRUE,
  'SettingsUrl' => '/plugin/ulogin',
  'SettingsPermission' => 'Garden.AdminUser.Only',
  'HasLocale' => TRUE,
  'RegisterPermissions' => FALSE,
  'Author' => "uLogin",
  'AuthorEmail' => 'team@ulogin.ru',
  'AuthorUrl' => 'http://ulogin.ru'
);

class uLoginPlugin extends Gdn_Plugin {
   /**
    * Act as a mini dispatcher for API requests to the plugin app
    */
    
    public function AuthenticationController_Render_Before($Sender, $Args) {
          if (isset($Sender->ChooserList)) {
             $Sender->ChooserList['ulogin'] = 'uLogin';
          }

          if (is_array($Sender->Data('AuthenticationConfigureList'))) {
             $List = $Sender->Data('AuthenticationConfigureList');
             $List['ulogin'] = '/dashboard/plugin/ulogin';
             $Sender->SetData('AuthenticationConfigureList', $List);
          }
      }    

      public function PluginController_uLogin_Create(&$Sender) {
          $Sender->Permission('Garden.Settings.Manage');
          $this->Dispatch($Sender, $Sender->RequestArgs);
      }

      public function Base_Render_Before($Sender) {
        $this->_SignInUser();
        $Sender->AddJsFile('http://ulogin.ru/js/ulogin.js');
        $Sender->AddJsFile('plugins/uLogin/js/init.js');
      }

      public function Controller_Toggle($Sender) {
        $this->AutoToggle($Sender);
      }

      public function Setup(){
        Gdn::Structure()
            ->Table('uLogin_User')
            ->Column('UserID', 'int(10)',FALSE, 'primary')
            ->Column('Identity', 'varchar(255)')
            ->Set(FALSE, FALSE);

      }
  
      public function OnDisable() {

      }
       /// Plugin Event Handlers ///

      public function EntryController_SignIn_Handler($Sender, $Args) {
          if (!$this->IsEnabled()) return;
          $Methods = $Sender->Data['Methods'];
          if (isset($Sender->Data['Methods'])) {
            $uPanel = $this->_GetPanel('MethodsPanel','small',true);
            $Method = array(
                'Name' => 'uLogin',
                'SignInHtml' => $uPanel);
            $Sender->Data['Methods'][] = $Method;
          }
       }

       public function Base_BeforeSignInButton_Handler($Sender, $Args) {
          if (!$this->IsEnabled()) return;

            echo "\n".$this->_GetPanel("BeforeSignInPanel","panel");
       }

      public function Base_BeforeSignInLink_Handler($Sender) {
        if (!$this->IsEnabled())
          return;
        if (!Gdn::Session()->IsValid())
          echo "\n".$this->_GetPanel();
      }

      /// Methods ///

      private function _GetPanel($id = '',$type='panel',$forced=false){
        $redirect = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];
        $panel_path = 'http://'.$_SERVER['SERVER_NAME'].$this->GetWebResource('views/upanel.php?id='.$id.'&type='.$type.'&redirect='.$redirect."&forced=".$forced);
        $panel = file_get_contents($panel_path);
        return $panel;
      }

      private function _isTokenExist(){
        return isset($_POST['token']) ? TRUE : FALSE;
      }

      private function _SignInUser(){
        if (!$this->_isTokenExist()) return;
        $Session = Gdn::Session();
        if ($Session->isValid()) return;  
        $data = file_get_contents('http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $_SERVER['HTTP_HOST']);
        $user = json_decode($data, true);
        if (!isset($user['error'])) {
            $UserData['Password'] = $this->_genPassword();
            $identity = parse_url($user['identity']);
            $UserData['Name'] = (isset($user['nickname']) ? $user['nickname'] : $user['last_name'].' '.$user['first_name']).' '.time();
            $UserData['Email'] = strpos($user['manual'],'email')===FALSE ? time().'_'.$user['email']: time().'_umf_'.$user['email'];
            $UserData['ShowEmail'] = '0';
            $UserData['Gender'] = isset($user['sex']) ?($user['sex'] == '2' ? 'm' : 'f') : 'm';
            $UserData['DateOfBirth'] = $user['bdate'];
            $UserData['DateLastActive'] = Gdn_Format::ToDateTime();
            $UserData['Photo'] = $user['photo'];
            $UserData['About'] = 'Country:'.$user['country'].' City:'.$user['city'].' My page:'. $user['identity'];

            $UserModel = new UserModel();
            $uLoginModel = new Gdn_Model('uLogin_User ');
            $UserID = false;
            $prefix = Gdn::Database()->SQL()->PrefixTable('uLogin_User');
            $Query = 'Select UserID From '.$prefix.' Where Identity=\''.$user['identity'].'\'';
            $uLoginUser = Gdn::Database()->Query($Query);
            if (count($uLoginUser->Result()) == 0){
                while($UserModel->GetByUsername($UserData['Name'])){
                    $UserData['Name'] = (isset($user['nickname']) ? $user['nickname'] : $user['last_name'].' '.$user['first_name']).' '.time();
                }
                while($UserModel->GetByEmail($UserData['Email'])){
                    $UserData['Email'] = strpos($user['manual'],'email')===FALSE ? time().'_'.$user['email']: time().'_umf_'.$user['email'];
                }
                $UserID = $UserModel->Save($UserData, array('ActivityType' => 'Join', 'CheckExisting' => TRUE, 'ValidateEmail' => FALSE, 'NoConfirmEmail' => TRUE));
                $UserModel->SaveRoles($UserID, C('Garden.Registration.DefaultRoles'));
                $Fields = array('UserID'=>  intval($UserID), 'Identity'=>$user['identity']);
                $uLoginModel->Insert($Fields);
            }else{
                $UserID = $uLoginUser->Value('UserID');
            }
            $UserModel->UpdateLastVisit($UserID, $Attributes);
            $Session->Start($UserID);
        }
      }

      private function _genPassword($length = 8){
        $randseed = md5(time());
        $maxlength = strlen($randseed);
        $idx = 0;
        $password = '';
        if ($length > $maxlength) {
          $length = $maxlength;
        }
        while ($idx < $length) {
          $char = substr($randseed, mt_rand(0, $maxlength-1), 1);
          $char = time() % 2 ? strtoupper($char) : $char;
          if (!strstr($password, $char)) {
            $password .= $char;
            $idx++;
          }
        }
        return $password;
      }

}