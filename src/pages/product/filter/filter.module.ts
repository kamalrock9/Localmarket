import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { FilterPage } from './filter';
import { TreeModule } from 'angular-tree-component';

@NgModule({
  declarations: [
    FilterPage,
  ],
  imports: [
    IonicPageModule.forChild(FilterPage),
    TranslateModule.forChild(),
    TreeModule
  ],
})
export class FilterPageModule {}
