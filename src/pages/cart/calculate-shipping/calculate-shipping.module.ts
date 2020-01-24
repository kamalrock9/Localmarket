import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { CalculateShippingPage } from './calculate-shipping';

@NgModule({
  declarations: [
    CalculateShippingPage,
  ],
  imports: [
    IonicPageModule.forChild(CalculateShippingPage),
    TranslateModule.forChild()
  ],
})
export class CalculateShippingPageModule {}
