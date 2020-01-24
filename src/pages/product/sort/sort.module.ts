import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { SortPage } from './sort';

@NgModule({
  declarations: [
    SortPage,
  ],
  imports: [
    IonicPageModule.forChild(SortPage),
    TranslateModule.forChild()
  ],
})
export class SortPageModule {}
