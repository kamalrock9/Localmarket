import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { AccountSettingPage } from './account-setting';

@NgModule({
  declarations: [
    AccountSettingPage,
  ],
  imports: [
    IonicPageModule.forChild(AccountSettingPage),
    TranslateModule.forChild()
  ],
})
export class AccountSettingPageModule {}
