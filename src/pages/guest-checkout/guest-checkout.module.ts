import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { GuestCheckoutPage } from './guest-checkout';

@NgModule({
  declarations: [
    GuestCheckoutPage,
  ],
  imports: [
    IonicPageModule.forChild(GuestCheckoutPage),
    TranslateModule.forChild(),
  ],
})
export class GuestCheckoutPageModule {}
