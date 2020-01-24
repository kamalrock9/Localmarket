import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { AddReviewPage } from './add-review';
import { Ionic2RatingModule } from 'ionic2-rating';

@NgModule({
  declarations: [
    AddReviewPage,
  ],
  imports: [
    IonicPageModule.forChild(AddReviewPage),
    Ionic2RatingModule
  ],
})
export class AddReviewPageModule {}
