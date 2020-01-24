import { ComponentsModule } from './../../../components/components.module';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { ReferEarnPage } from './refer-earn';
import { TranslateModule } from '@ngx-translate/core';

@NgModule({
  declarations: [
    ReferEarnPage,
  ],
  imports: [
    IonicPageModule.forChild(ReferEarnPage),
    TranslateModule.forChild(),
    ComponentsModule
  ],
})
export class ReferEarnPageModule {}
