import { IonicImageLoader } from 'ionic-image-loader';
import { TreeModule } from 'angular-tree-component';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { SearchPage } from './search';
import { TranslateModule } from '@ngx-translate/core';

@NgModule({
  declarations: [
    SearchPage,
  ],
  imports: [
    IonicPageModule.forChild(SearchPage),
    TranslateModule.forChild(),
    IonicImageLoader,
    TreeModule
  ],
})
export class SearchPageModule {}
