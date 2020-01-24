import { HttpClient } from '@angular/common/http';
import { TranslateModule, TranslateLoader } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { LanguageSettingPage } from './language-setting';
import { createTranslateLoader } from '../../../app/app.module';

@NgModule({
  declarations: [
    LanguageSettingPage,
  ],
  imports: [
    IonicPageModule.forChild(LanguageSettingPage),
    TranslateModule.forChild({
      loader: {
        provide: TranslateLoader,
        useFactory: (createTranslateLoader),
        deps: [HttpClient]
      }
    })
  ],
})
export class LanguageSettingPageModule {}
