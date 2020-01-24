import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { MiniCartPage } from './mini-cart';
import { TranslateModule } from '@ngx-translate/core';

@NgModule({
  declarations: [
    MiniCartPage,
  ],
  imports: [
    IonicPageModule.forChild(MiniCartPage),
    TranslateModule.forChild()
  ],
})
export class MiniCartPageModule { }
