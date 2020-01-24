import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { CartPage } from './cart';
import { IonicImageLoader } from 'ionic-image-loader';

@NgModule({
  declarations: [
    CartPage,
  ],
  imports: [
    IonicPageModule.forChild(CartPage),
    TranslateModule.forChild(),
    IonicImageLoader
  ],
})
export class CartPageModule {}
