import { TranslateModule } from '@ngx-translate/core';
import { PipesModule } from './../../pipes/pipes.module';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { PaymentPage } from './payment';

@NgModule({
  declarations: [
    PaymentPage,
  ],
  imports: [
    IonicPageModule.forChild(PaymentPage),
    TranslateModule.forChild(),
    PipesModule
  ],
})
export class PaymentPageModule {}
