import { PipesModule } from './../../../pipes/pipes.module';
import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { ReviewsPage } from './reviews';
import { Ionic2RatingModule } from 'ionic2-rating/';

@NgModule({
  declarations: [
    ReviewsPage,
  ],
  imports: [
    IonicPageModule.forChild(ReviewsPage),
    TranslateModule.forChild(),
    PipesModule,
    Ionic2RatingModule 
  ],
})
export class ReviewsPageModule {}
