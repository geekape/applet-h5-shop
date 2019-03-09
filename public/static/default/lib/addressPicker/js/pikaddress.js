/**
 * Created by chenliang on 7/7/15.
 */
(function (root, factory) {
	if (typeof exports === 'object') {
		module.exports = factory();
	} else if (typeof define === 'function' && define.amd) {
		// AMD. Register as an anonymous module.
		define(function () {
			return factory();
		});
	} else {
		root.AAPcdPicker = factory();
	}
}(this, function () {


	/*
	 * CSS class name constants
	 */
	var kTABS = 'pikaddress-tabs',
		kTAB = 'pikaddress-tab',
		kTAB__SELECTED = 'pikaddress-tab--selected',
		kTAB_OFTEN = 'pikaddress-tabOften',
		kCONTENT_OFTEN = 'pikaddress-contentOften',
		kTAB_PROVINCE = 'pikaddress-tabProvince',
		kCONTENT_PROVINCE = 'pikaddress-contentProvince',
		kTAB_CITY = 'pikaddress-tabCity',
		kCONTENT_CITY = 'pikaddress-contentCity',
		kTAB_COUNTRY = 'pikaddress-tabCountry',
		kCONTENT_COUNTRY = 'pikaddress-contentCountry',
		kDOMADDRESS = 'pikaddress-address',
		kDOMADDRESS__SELECTED = 'pikaddress-address--selected',
		KDOMADDRESS__HOVER = 'pikaddress-address--hover';

	/*
	 * Default settings
	 */
	var defaults = {
		filed: null,
		districtsData: null,
		district: null,
		districtOften: null,
		onSelectDone: null,
		onFieldFocus: null,
	};

	/*
	 * helper method
	 */

	/**
	 * Determine if disA is or contained in disB
	 *
	 * Because the district data is always in form of "Province-city-country", so just compare them with the String method
	 * @param disA
	 * @param disB
	 */
	isDistrictContained = function (disA, disB) {
		if(!disA || !disB) return false;
		return disB.indexOf(disA) != -1 ? true : false;
	}

	/**
	 * templating functions to abstract HTML rendering
	 */
	renderTabs = function () {
		var $tabs = $('<div />', {
			class: 'pikaddress-tabs clearfix'
		});

		var $tabOften = $('<div />', {
			class: 'pikaddress-tab pikaddress-tabOften pikaddress-tab--selected',
		}).append('<span class="pikaddress-tab-sapn">常用</span>');
		;

		var $tabProvince = $('<div />', {
			class: 'pikaddress-tab pikaddress-tabProvince',
		}).append('<span class="pikaddress-tab-sapn">省</span>');

		var $tabCity = $('<div />', {
			class: 'pikaddress-tab pikaddress-tabCity',
		}).append('<span class="pikaddress-tab-sapn">市</span>');
		;

		var $tabCountry = $('<div />', {
			class: 'pikaddress-tab pikaddress-tabCountry',
		}).append('<span class="pikaddress-tab-sapn">区县</span>');
		;

		$tabs.append($tabOften, $tabProvince, $tabCity, $tabCountry);
		return $tabs;
	};

	renderContentOften = function (data) {
		var $eleOften = $('<div />', {
			class: kCONTENT_OFTEN,
		})
		var selectedDistrict = data.district;
		var districtArray = data.districtsOften;
		for (var i = 0; i < districtArray.length; i++) {
			var district = districtArray[i];
			var $districtEle = $('<span/>', {
				class: kDOMADDRESS + (isDistrictContained( district , selectedDistrict) ? ' ' + kDOMADDRESS__SELECTED : ''),
				'data-district': district,
				html: district.split('-').slice(-1)[0]
			});

			$eleOften.append($districtEle);
		}
		return $eleOften;
	};

	renderContentProvince = function (data) {
		var $eleProvince = $('<div />', {
			class: kCONTENT_PROVINCE,
		});

		var districts = data.districtsData;
		var selectedDistrict = data.district;

		for (var i = 0; i < districts.length; i++) {
			var district = districts[i];
			var provinceName = district['name'];
			var $districtEle = $('<span/>', {
				class: kDOMADDRESS + (isDistrictContained(provinceName, selectedDistrict)? ' ' + kDOMADDRESS__SELECTED : ''),
				'data-district': provinceName, // eg: '江苏省'
				html: provinceName
			});
			$eleProvince.append($districtEle);
		}
		return $eleProvince;

	};

	renderContentCity = function (data) {
		var $eleCity = $('<div />', {
			class: kCONTENT_CITY,
			backgroundColor: 'blue'
		})
		var provinceName = getProvinceName(data.district),
			citysInProvince = getCitiesOfProvince(provinceName, data.districtsData);

		for (var i = 0; i < citysInProvince.length; i++) {
			var cityName = citysInProvince[i]['name'];
			var $cityEle = $('<span/>', {
				class: kDOMADDRESS + (isDistrictContained(cityName, data.district)? ' ' + kDOMADDRESS__SELECTED : ''),
				'data-district': provinceName + '-' + cityName,
				html: cityName
			});
			$eleCity.append($cityEle);
		}
		return $eleCity;

	};

	renderContentCountry = function (data) {
		var $eleCountry = $('<div />', {
			class: kCONTENT_COUNTRY,
			backgroundColor: 'red'
		})

		var provinceName = getProvinceName(data.district),
			cityName = getCityName(data.district),
			countriesInCity = getCountriesOfCity(provinceName, cityName, data.districtsData);

		for (var i = 0; i < countriesInCity.length; i++) {
			var countryName = countriesInCity[i];
			var $countryEle = $('<span/>', {
				class: kDOMADDRESS + (isDistrictContained(countryName , data.district) ? ' ' + kDOMADDRESS__SELECTED : ''),
				'data-district': provinceName + '-' + cityName + '-' + countryName,
				html: countryName
			});
			$eleCountry.append($countryEle);
		}
		return $eleCountry;
	};
	getProvinceName = function (district) {
		return district.split('-')[0];
	};
	getCityName = function (district) { // note: must ensure that have city
		return district.split('-')[1];
	};
	getCountryName = function (distric) {// note: must ensure that have country after split
		return distric.split('-')[2];
	};
	getCitiesOfProvince = function (provinceName, data) {
		var cities = [];
		for (var i = 0; i < data.length; i++) {
			var pName = data[i]['name'];
			if (pName == provinceName) {
				cities = data[i]['city'];
				break;
			}
		}
		return cities;
	};
	getCountriesOfCity = function (provinceName, cityName, data) {
		var cities = getCitiesOfProvince(provinceName, data),
			countries = [];
		for (var i = 0; i < cities.length; i++) {
			if (cities[i]['name'] == cityName) {
				countries = cities[i]['area']
				break;
			}
		}
		return countries;
	};


	var isClickOnElement = function (e, ele) {
		var x = e.clientX;
		var y = e.clientY;
		var eleW = $(ele).width();
		var eleH = $(ele).height();
		var eleL = $(ele).position().left;
		var eleT = $(ele).position().top;
		if ((eleL <= x && x <= eleL + eleW) && (eleT <= y && y <= eleT + eleH)) {
			return true;
		} else {
			return false;
		}
	}

	/*
	 * AAPcdPicker constructor
	 */
	 AAPcdPicker = function (options) {
		var self = this,
			opts = this.config(options);


		self._onClick = function (e) {
			e.stopPropagation();  //防止 click 事件冒泡触发 ._onDocumentClick 方法执行
			var $target = $(e.target);

			// case1: 点击tab
			if ($target.hasClass(kTAB) || $target.parent().hasClass(kTAB)) {
				self.clickTab($target);
			}

			// case2: 点击district-address
			if ($target.hasClass(kDOMADDRESS)) {
				var selectedDistrict = $target.attr('data-district');
				self.setDistrict(selectedDistrict);
			};

			self._c = true; // 用这个变量来阻止 blur 事件触发的函数隐藏该组件

		};

		self._onDocumentClick = function (e) {
			var pEl = e.target;
			if (!pEl) return;

			if (pEl === opts.field) {
				return;
			}

			do {
				if ($(pEl).hasClass('pikaddress')) {
					return;
				}
			}
			while ((pEl = pEl.parentNode));
			self.hide();
		}

		self._onFieldClick = function (e) {
			e.stopPropagation(); //防止 click 事件冒泡触发 ._onDocumentClick 方法执行
			if (self._o.onFieldFocus) {
				self._o.onFieldFocus();
			}
			if(self._v == true) return;
			self.show();
		}

		self._onFieldBlur = function() {
			if(self._o.onFieldBlur) {
				self._o.onFieldBlur();
			}

			if(!self._c) {
				self.hide();
			}
			self._c = false;
		}

		self.el = $('<div />', {
			class: 'pikaddress'
		});
		self.el.appendTo($(document.body));
    self.el.hide();
		self.el.on('mousedown', self._onClick);
		opts.$field.on('click', self._onFieldClick);
		opts.$field.on('focus', self._onFieldClick);
		opts.$field.on('blur', self._onFieldBlur);
	}


	AAPcdPicker.prototype = {
		config: function (options) {
			this._o = $.extend({}, defaults, options);
			this._o.$field = $(this._o.field);
			return this._o;
		},

		//
		setSelectedTab: function (tabname) {
			this._o.selectedTabname = tabname;
			var $tabs = this.el.find('.pikaddress-tab'),
				$tab = this.el.find('.' + tabname);
			$tabs.removeClass('pikaddress-tab--selected');
			$tab.addClass('pikaddress-tab--selected');

			var $contentContainer = this.el.find('.pikaddress-content');
			$contentContainer.empty();

			if (tabname == kTAB_OFTEN) {
				$contentContainer.append(renderContentOften(this._o));
			}
			if (tabname == kTAB_PROVINCE) {
				$contentContainer.append(renderContentProvince(this._o));
			}
			if (tabname == kTAB_CITY) {
				$contentContainer.append(renderContentCity(this._o));
			}
			if (tabname == kTAB_COUNTRY) {
				$contentContainer.append(renderContentCountry(this._o));
			}
		},
		switchToSelectedTabContent: function () {
			var $selectedTab = this.el.find('.' + kTAB__SELECTED);
			var selectedTabName = '';
			if ($selectedTab.hasClass(kTAB_OFTEN)) {
				selectedTabName = kTAB_OFTEN;
			}
			if ($selectedTab.hasClass(kTAB_PROVINCE)) {
				selectedTabName = kTAB_PROVINCE;
			}
			if ($selectedTab.hasClass(kTAB_CITY)) {
				selectedTabName = kTAB_CITY;
			}
			if ($selectedTab.hasClass(kTAB_COUNTRY)) {
				selectedTabName = kTAB_COUNTRY;
			}
			this.setSelectedTab(selectedTabName)

		},

		// refresh the view
		draw: function () {
			var $el = $(this.el);

			var $tabs = renderTabs();
			$el.empty();
			$el.append($tabs)

			var $contentContainer = $('<div/>', {
				class: 'pikaddress-content'
			});
			$el.append($contentContainer);
			this.switchToSelectedTabContent();
		},

		adjustPosition: function () {
			var $field = $(this._o.field),
				$el = this.el;
			var x = $field.position().left,
				y = $field.position().top + $field.outerHeight();

			$el.css({
				position: 'absolute',
				top: y ,
				left: x,
			});
		},
		show: function () {
			if (this._v) {
				return;
			}
			this._v = true;
			$(document).on('click', this._onDocumentClick);
			this.draw();
			this.adjustPosition();
			this.el.show();

		},
		hide: function () {
			this._v = false;
			this.el.hide();
			$(document).off('click', this._onDocumentClick);

		},

		setDistrict: function(district){
			this._o.district = district;

			var isDistrictProvince = district.split('-').length == 1,
				isDistrictCity = district.split('-').length == 2,
				isDistrictCountry = district.split('-').length == 3;

			if (isDistrictProvince) {
				this.setSelectedTab(kTAB_CITY)
				return;
			}
			if (isDistrictCity) {
				this.setSelectedTab(kTAB_COUNTRY)
				return;
			}
			if (isDistrictCountry) {
				if (this._o.onSelectDone) {
					this._o.onSelectDone();
				}
				return;
			}
		},

		clickTab: function($target) {
			var $tab;
			if ($target.hasClass(kTAB)) {
				$tab = $target;
			} else if ($target.parent().hasClass(kTAB)) {
				$tab = $target.parent();
			}
			if ($tab.hasClass('pikaddress-tab--selected')) {
				return;
			}


			// 常用
			if ($tab.hasClass(kTAB_OFTEN)) {
				this.setSelectedTab(kTAB_OFTEN);
			}
			// 省
			if ($tab.hasClass(kTAB_PROVINCE)) {
				this.setSelectedTab(kTAB_PROVINCE);
			}
			// 市
			if ($tab.hasClass(kTAB_CITY)) {
				this.setSelectedTab(kTAB_CITY);
			}
			// 区县
			if ($tab.hasClass(kTAB_COUNTRY)) {
				this.setSelectedTab(kTAB_COUNTRY);
			}

		}
	}
	return AAPcdPicker;
}))


