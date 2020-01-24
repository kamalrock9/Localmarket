import { ComponentsModule } from './../../components/components.module';
import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { CategoriesPage } from './categories';
import { Ionic2RatingModule } from 'ionic2-rating';
import { TreeModule } from 'angular-tree-component';
import { IonicImageLoader } from 'ionic-image-loader';

@NgModule({
  declarations: [
    CategoriesPage,
  ],
  imports: [
    Ionic2RatingModule,
    IonicPageModule.forChild(CategoriesPage),
    TranslateModule.forChild(),
    IonicImageLoader,
    ComponentsModule,
    TreeModule
  ],
})
export class CategoriesPageModule {
}
