import { TranslateModule } from '@ngx-translate/core';
import { PipesModule } from './../../pipes/pipes.module';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { OrdersPage } from './orders';

@NgModule({
  declarations: [
    OrdersPage,
  ],
  imports: [
    IonicPageModule.forChild(OrdersPage),
    TranslateModule.forChild(),
    PipesModule
  ],
})
export class OrdersPageModule {}
