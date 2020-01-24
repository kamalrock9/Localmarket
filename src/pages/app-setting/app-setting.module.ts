import { TranslateModule } from '@ngx-translate/core';

import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { AppSettingPage } from './app-setting';

@NgModule({
  declarations: [
    AppSettingPage,
  ],
  imports: [
    IonicPageModule.forChild(AppSettingPage),
    TranslateModule.forChild()
  ],
})
export class AppSettingPageModule {}
