import { Pipe, PipeTransform } from '@angular/core';
import { CurrencyPipe } from '@angular/common';

@Pipe({
  name: 'money',
})
export class MoneyPipe implements PipeTransform {
  app: any = {};
  constructor() { }
  transform(value, app) {
    //console.log(app);
    let x = app;
    return new CurrencyPipe('en-US').transform(value, x.currency, "symbol", '1.' + x.number_of_decimals + '-' + x.number_of_decimals);
  }
}
