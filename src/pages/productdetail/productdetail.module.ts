import { IonicImageLoader } from 'ionic-image-loader';
import { TranslateModule } from '@ngx-translate/core';
import { PipesModule } from './../../pipes/pipes.module';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { ProductdetailPage } from './productdetail';
import { Ionic2RatingModule } from 'ionic2-rating';

@NgModule({
  declarations: [
    ProductdetailPage
  ],
  imports: [
    IonicPageModule.forChild(ProductdetailPage),
    TranslateModule.forChild(),
    Ionic2RatingModule,
    IonicImageLoader,
    PipesModule
  ],
  exports: [
  ]
})
export class ProductdetailPageModule { }
