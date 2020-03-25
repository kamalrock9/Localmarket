import { ComponentsModule } from './../../components/components.module';
import { IonicImageLoader } from 'ionic-image-loader';
import { TranslateModule } from '@ngx-translate/core';
import { PipesModule } from './../../pipes/pipes.module';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { HomePage } from './home';
import { Ionic2RatingModule } from 'ionic2-rating';


@NgModule({
  declarations: [
    HomePage
  ],
  imports: [  
    IonicPageModule.forChild(HomePage),
    Ionic2RatingModule,
    PipesModule,
    TranslateModule.forChild(),
    IonicImageLoader,
    ComponentsModule
  ],
})
export class HomePageModule {
}
