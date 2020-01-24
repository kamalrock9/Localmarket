import { ComponentsModule } from './../../components/components.module';
import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { TosPage } from './tos';

@NgModule({
  declarations: [
    TosPage,
  ],
  imports: [
    IonicPageModule.forChild(TosPage),
    TranslateModule.forChild(),
    ComponentsModule
  ],
})
export class TosPageModule {}
