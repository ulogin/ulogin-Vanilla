<?php if (!defined('APPLICATION')) exit();
?>
<style type="text/css">
.Configuration {
   margin: 0 20px 20px;
   background: #f5f5f5;
   float: left;
}
.ConfigurationForm {
   padding: 20px;
   float: left;
}
#Content form .ConfigurationForm ul {
   padding: 0;
}
#Content form .ConfigurationForm input.Button {
   margin: 0;
}
</style>
<h1><?php echo $this->Data('Title'); ?></h1>
<?php
echo $this->Form->Open();
echo $this->Form->Errors();
?>
<div class="Configuration">
   <div class="ConfigurationForm">
      <ul>
         <li>
            <?php
               echo $this->Form->Label('Widget type', 'Widget type');
               echo $this->Form->RadioList('display',array('panel' => 'Panel<br/>', 'small'=>'Small panel<br/>','window'=>'Popup window'));
               echo $this->Form->Label('SignIn widget type', 'Widget type at SignIn window');
               echo $this->Form->RadioList('display2',array('panel' => 'Panel<br/>', 'small'=>'Small panel<br/>','window'=>'Popup window'));
               echo $this->Form->Label('Providers', 'Providers');
               echo $this->Form->CheckBoxList('providers',array('VKontakte' => 'vkontakte', 'Odnoklassniki.ru'=>'odnoklassniki','Mail.ru'=>'mailru','Facebook'=>'facebook','Twitter' => 'twitter', 'Google'=>'google','Yandex'=>'yandex','LiveJournal'=>'livejournal', 'Open ID' => 'openid'));
               echo $this->Form->Label('Drop down menu', 'Drop down menu');
               echo $this->Form->CheckBoxList('hidden',array('VKontakte' => 'vkontakte', 'Odnoklassniki.ru'=>'odnoklassniki','Mail.ru'=>'mailru','Facebook'=>'facebook','Twitter' => 'twitter', 'Google'=>'google','Yandex'=>'yandex','LiveJournal'=>'livejournal', 'Open ID' => 'openid'));
            ?>
         </li>
      </ul>
      <?php echo $this->Form->Button('Save settings', array('class' => 'Button SliceSubmit')); ?>
   </div>
</div>
<?php 
   echo $this->Form->Close();
