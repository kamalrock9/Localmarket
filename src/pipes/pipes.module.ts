import { MomentPipe } from './moment/moment';
import { MoneyPipe } from './money/money';
import { DiscountPipe } from './discount/discount';
import { NgModule } from '@angular/core';
@NgModule({
	declarations: [ 
		DiscountPipe,
		MoneyPipe,
		MomentPipe
	],
	imports: [],
	exports: [
		DiscountPipe,
		MoneyPipe,
		MomentPipe
	]
})
export class PipesModule { }
