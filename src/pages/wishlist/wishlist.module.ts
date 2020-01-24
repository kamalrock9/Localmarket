import { IonicImageLoader } from 'ionic-image-loader';
import { TranslateModule } from '@ngx-translate/core';
import { Ionic2RatingModule } from 'ionic2-rating';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { WishlistPage } from './wishlist';

@NgModule({
  declarations: [
    WishlistPage,
  ],
  imports: [
    IonicPageModule.forChild(WishlistPage),
    TranslateModule.forChild(),
    IonicImageLoader,
    Ionic2RatingModule
  ],
})
export class WishlistPageModule {}
