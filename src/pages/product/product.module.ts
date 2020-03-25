import { ComponentsModule } from './../../components/components.module';
import { TranslateModule } from '@ngx-translate/core';
import { Ionic2RatingModule } from 'ionic2-rating';
import { PipesModule } from './../../pipes/pipes.module';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { ProductPage } from './product';
import { IonicImageLoader } from 'ionic-image-loader';

@NgModule({
  declarations: [
    ProductPage,
  ],
  imports: [
    IonicPageModule.forChild(ProductPage),
    TranslateModule.forChild(),
    ComponentsModule,
    Ionic2RatingModule,
    IonicImageLoader,
    PipesModule
  ],
})
export class ProductPageModule {}
