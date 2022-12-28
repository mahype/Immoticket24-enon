/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./assets/js/editor/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./assets/js/editor/blocks/affiliate-area/edit.js":
/*!********************************************************!*\
  !*** ./assets/js/editor/blocks/affiliate-area/edit.js ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__);

/**
 * Affiliate Content Edit Component.
 *
 * @since 2.8
 */

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */


var allowedBlocks = ['affiliatewp/registration', 'affiliatewp/login', 'wpforms/form-selector'];
var template = [['affiliatewp/registration'], ['affiliatewp/login']];

/**
 * Affiliate Area.
 *
 * Affiliate area block component.
 *
 * @since 2.8
 *
 * @returns {JSX.Element} The rendered component.
 */
function AffiliateArea() {
  var useInnerBlocksProps = _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["useInnerBlocksProps"] ? _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["useInnerBlocksProps"] : _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["__experimentalUseInnerBlocksProps"];
  var blockProps = Object(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["useBlockProps"])({
    className: classnames__WEBPACK_IMPORTED_MODULE_1___default()('affwp-affiliate-area')
  });
  var innerBlocksProps = useInnerBlocksProps(blockProps, {
    template: template,
    allowedBlocks: allowedBlocks
  });
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", innerBlocksProps);
}
/* harmony default export */ __webpack_exports__["default"] = (AffiliateArea);

/***/ }),

/***/ "./assets/js/editor/blocks/affiliate-area/index.js":
/*!*********************************************************!*\
  !*** ./assets/js/editor/blocks/affiliate-area/index.js ***!
  \*********************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _components_icon__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../components/icon */ "./assets/js/editor/components/icon.js");
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit */ "./assets/js/editor/blocks/affiliate-area/edit.js");
/* harmony import */ var _save__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./save */ "./assets/js/editor/blocks/affiliate-area/save.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/**
 * Affiliate Area Block.
 *
 * @since 2.8
 */

/**
 * Internal Dependencies
 */




/**
 * WordPress Dependencies
 */

var name = 'affiliatewp/affiliate-area';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Affiliate Area', 'affiliate-wp'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Displays the affiliate registration and login forms to a logged out user. A logged-in user will see the Affiliate Area instead of these forms.', 'affiliate-wp'),
  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Affiliate Area', 'affiliate-wp'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Area', 'affiliate-wp'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Dashboard', 'affiliate-wp')],
  category: 'affiliatewp',
  icon: _components_icon__WEBPACK_IMPORTED_MODULE_0__["default"],
  supports: {
    html: false
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_1__["default"],
  save: _save__WEBPACK_IMPORTED_MODULE_2__["default"]
};


/***/ }),

/***/ "./assets/js/editor/blocks/affiliate-area/save.js":
/*!********************************************************!*\
  !*** ./assets/js/editor/blocks/affiliate-area/save.js ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return save; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__);

/**
 * WordPress dependencies
 */

function save(_ref) {
  var attributes = _ref.attributes;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__["InnerBlocks"].Content, null);
}

/***/ }),

/***/ "./assets/js/editor/blocks/affiliate-content/edit.js":
/*!***********************************************************!*\
  !*** ./assets/js/editor/blocks/affiliate-content/edit.js ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return AffiliateContentEdit; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _components_icon__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../components/icon */ "./assets/js/editor/components/icon.js");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__);

/**
 * Affiliate Content Edit Component.
 *
 * @since 2.8
 */

/**
 * Internal dependencies
 */


/**
 * WordPress dependencies
 */





/**
 * Affiliate Content Edit.
 *
 * Affiliate content edit component.
 *
 * @since 2.8
 *
 * @param {string} className The class name for the content wrapper.
 * @returns {JSX.Element}    The rendered component.
 */
function AffiliateContentEdit(_ref) {
  var className = _ref.className;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
    className: className
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__["InnerBlocks"], null));
}
var withNotice = Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_4__["createHigherOrderComponent"])(function (BlockListBlock) {
  return function (props) {
    if (props.isSelected) {
      // Get ID of parent block.
      var parentBlockId = wp.data.select('core/block-editor').getBlockHierarchyRootClientId(props.clientId);
      if (parentBlockId && props.name !== 'affiliatewp/affiliate-content') {
        // Get parent block.
        var parentBlock = wp.data.select('core/block-editor').getBlock(parentBlockId);

        // If the parent block is the affiliate content block, show a message.
        if ('affiliatewp/affiliate-content' === parentBlock.name) {
          return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(BlockListBlock, props), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["Notice"], {
            isDismissible: false
          }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["Icon"], {
            icon: _components_icon__WEBPACK_IMPORTED_MODULE_1__["default"],
            color: true
          }), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('This block will only be shown to affiliates', 'affiliate-wp')));
        }
      }
    }
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(BlockListBlock, props);
  };
}, 'withNotice');
wp.hooks.addFilter('editor.BlockListBlock', 'affiliate-wp/with-notice', withNotice);

/***/ }),

/***/ "./assets/js/editor/blocks/affiliate-content/index.js":
/*!************************************************************!*\
  !*** ./assets/js/editor/blocks/affiliate-content/index.js ***!
  \************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _components_icon__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../components/icon */ "./assets/js/editor/components/icon.js");
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./edit */ "./assets/js/editor/blocks/affiliate-content/edit.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__);

/**
 * Affiliate Content Block.
 *
 * @since 2.8
 */

/**
 * Internal Dependencies
 */



/**
 * WordPress Dependencies
 */


var name = 'affiliatewp/affiliate-content';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Affiliate Content', 'affiliate-wp'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Restrict content to logged-in affiliates.', 'affiliate-wp'),
  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Content', 'affiliate-wp'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Restrict', 'affiliate-wp')],
  category: 'affiliatewp',
  icon: _components_icon__WEBPACK_IMPORTED_MODULE_1__["default"],
  supports: {
    html: false
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_2__["default"],
  save: function save(_ref) {
    var className = _ref.className;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      className: className
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["InnerBlocks"].Content, null));
  }
};


/***/ }),

/***/ "./assets/js/editor/blocks/affiliate-creative/edit.js":
/*!************************************************************!*\
  !*** ./assets/js/editor/blocks/affiliate-creative/edit.js ***!
  \************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "./node_modules/@babel/runtime/helpers/toConsumableArray.js");
/* harmony import */ var _babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "./node_modules/@babel/runtime/helpers/asyncToGenerator.js");
/* harmony import */ var _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "./node_modules/@babel/runtime/helpers/slicedToArray.js");
/* harmony import */ var _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/regenerator */ "@babel/runtime/regenerator");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _components_affiliate_creative__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../components/affiliate-creative */ "./assets/js/editor/components/affiliate-creative.js");
/* harmony import */ var _components_icon__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../components/icon */ "./assets/js/editor/components/icon.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! @wordpress/url */ "@wordpress/url");
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(_wordpress_url__WEBPACK_IMPORTED_MODULE_11__);





/**
 * Affiliate Creative Component.
 *
 * @since 2.8
 */

/**
 * Internal dependencies
 */



/**
 * WordPress dependencies
 */







/**
 * Affiliate Creatives Edit.
 *
 * Affiliate creative component.
 *
 * @since 2.8
 *
 * @param {object}   attributes    Block attributes.
 * @param {function} setAttributes Method used to set the attributes for this component in the global scope.
 * @returns {JSX.Element}          The rendered component.
 */
function AffiliateCreativeEdit(_ref) {
  var attributes = _ref.attributes,
    setAttributes = _ref.setAttributes;
  // The creative ID.
  var id = attributes.id;
  var _useState = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["useState"])(null),
    _useState2 = _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_2___default()(_useState, 2),
    creatives = _useState2[0],
    setCreatives = _useState2[1];
  var _useState3 = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["useState"])(null),
    _useState4 = _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_2___default()(_useState3, 2),
    creative = _useState4[0],
    setCreative = _useState4[1];
  var _useState5 = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["useState"])(false),
    _useState6 = _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_2___default()(_useState5, 2),
    isLoading = _useState6[0],
    setLoading = _useState6[1];
  var _useState7 = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["useState"])(),
    _useState8 = _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_2___default()(_useState7, 2),
    error = _useState8[0],
    setError = _useState8[1];
  var CREATIVES_QUERY = {
    number: 100 // Hardcoded limit for now.
  };

  /**
   * Fetch a list of creatives.
   */
  Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["useEffect"])(function () {
    var ignore = false;
    if (creatives) {
      return;
    }
    function fetchData() {
      return _fetchData.apply(this, arguments);
    } // Fetch the creatives.
    function _fetchData() {
      _fetchData = _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1___default()( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_4___default.a.mark(function _callee() {
        var result;
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_4___default.a.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                setLoading(true);
                _context.prev = 1;
                _context.next = 4;
                return _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_10___default()({
                  path: Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_11__["addQueryArgs"])("/affwp/v1/creatives/", CREATIVES_QUERY)
                });
              case 4:
                result = _context.sent;
                if (!ignore) {
                  // Store the creatives in state.
                  setCreatives(result);

                  /**
                   * Instantly store the saved creative object in state so we
                   * can pass the values to <AffiliateCreative />
                   */
                  if (id) {
                    // Find the creative based on the ID we have saved.
                    setCreative(result.find(function (creative) {
                      return creative.creative_id === id;
                    }));
                  }
                }
                _context.next = 11;
                break;
              case 8:
                _context.prev = 8;
                _context.t0 = _context["catch"](1);
                setError(_context.t0);
              case 11:
                setLoading(false);
              case 12:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, null, [[1, 8]]);
      }));
      return _fetchData.apply(this, arguments);
    }
    fetchData();
    return function () {
      ignore = true;
    };
  }, []);
  Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["useEffect"])(function () {
    if (creative) {
      // After the creative is updated, set the attribute ID.
      setAttributes({
        id: parseInt(creative.creative_id)
      });
    }
  }, [creative]);
  var affiliateWpIcon = function affiliateWpIcon() {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__["Icon"], {
      icon: _components_icon__WEBPACK_IMPORTED_MODULE_6__["default"],
      color: true
    });
  };
  var isActiveCreative = function isActiveCreative(creative) {
    if ('active' === creative.status) {
      return true;
    }
    return false;
  };

  // Check to see if a creative exists.
  var isCreative = function isCreative(creativeId) {
    return creatives.find(function (creative) {
      return creative.creative_id === creativeId;
    });
  };
  var CreativeSelector = function CreativeSelector() {
    var creativesOptions = function creativesOptions() {
      var selectOption = {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__["__"])('- Select -', 'affiliate-wp'),
        value: '',
        disabled: true
      };
      var creativesOptions = creatives.map(function (creative) {
        return {
          label: "(id: ".concat(creative.id, ") ").concat(creative.name),
          value: creative.id
        };
      });
      return [selectOption].concat(_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0___default()(creativesOptions));
    };
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__["SelectControl"], {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__["__"])('Select the affiliate creative to display', 'affiliate-wp'),
      value: id && typeof id !== 'undefined' && isCreative(id) ? id : '',
      options: creativesOptions(),
      onChange: function onChange(id) {
        return setCreative(creatives.find(function (creative) {
          return creative.creative_id === parseInt(id);
        }));
      }
    }));
  };
  var CreativeInspectorControls = function CreativeInspectorControls() {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_9__["InspectorControls"], null, creative && id && !isActiveCreative(creative) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__["Notice"], {
      className: "affwp-block-inspector-notice",
      isDismissible: false,
      status: "warning"
    }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__["__"])('This creative is inactive.', 'affiliate-wp')), id && !isCreative(id) && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__["Notice"], {
      className: "affwp-block-inspector-notice",
      isDismissible: false,
      status: "error"
    }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__["sprintf"])(Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__["__"])('This creative (id: %d) no longer exists.', 'affiliate-wp'), id)), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__["PanelBody"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(CreativeSelector, null)));
  };
  if (isLoading) {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__["Spinner"], null);
  }

  /**
   * If there are no creatives and there is no current creative set
   * (in block's attributes), show an error message.
   */
  if (creatives === null && creative === null && !id) {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__["Placeholder"], {
      icon: affiliateWpIcon,
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__["__"])('Affiliate Creative', 'affiliate-wp')
    }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__["__"])('No creatives were found.', 'affiliate-wp')));
  }

  /**
   * If there is no creative set at all, allow the user to select one.
   *
   * 1. No creative was ever selected.
   * 2. A creative was previously saved, but the ID no longer exists (deleted)
   */
  if (creative === null && creatives !== null && !id ||
  // 1
  creatives !== null && id && !isCreative(id) // 2
  ) {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(CreativeInspectorControls, null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__["Placeholder"], {
      icon: affiliateWpIcon,
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__["__"])('Affiliate Creative', 'affiliate-wp')
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(CreativeSelector, null)));
  }

  /**
   * If there's a creative (in state) and the ID has been set in the block
   * attributes, show the creative preview.
   */
  if (creative && id) {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(CreativeInspectorControls, null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__["createElement"])(_components_affiliate_creative__WEBPACK_IMPORTED_MODULE_5__["default"], {
      id: creative.creative_id,
      name: creative.name,
      description: creative.description,
      image: creative.image,
      url: creative.url,
      text: creative.text,
      preview: true
    }));
  }
  return null;
}
/* harmony default export */ __webpack_exports__["default"] = (AffiliateCreativeEdit);

/***/ }),

/***/ "./assets/js/editor/blocks/affiliate-creative/index.js":
/*!*************************************************************!*\
  !*** ./assets/js/editor/blocks/affiliate-creative/index.js ***!
  \*************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _components_icon__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../components/icon */ "./assets/js/editor/components/icon.js");
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit */ "./assets/js/editor/blocks/affiliate-creative/edit.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/**
 * Affiliate Creative Block.
 *
 * @since 2.8
 */

/**
 * Internal Dependencies
 */



/**
 * WordPress Dependencies
 */

var name = 'affiliatewp/affiliate-creative';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Affiliate Creative', 'affiliate-wp'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Show an affiliate creative.', 'affiliate-wp'),
  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Creative', 'affiliate-wp'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Banner', 'affiliate-wp')],
  category: 'affiliatewp',
  icon: _components_icon__WEBPACK_IMPORTED_MODULE_0__["default"],
  supports: {
    html: false
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_1__["default"],
  save: function save() {
    return null;
  }
};


/***/ }),

/***/ "./assets/js/editor/blocks/affiliate-creatives/edit.js":
/*!*************************************************************!*\
  !*** ./assets/js/editor/blocks/affiliate-creatives/edit.js ***!
  \*************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "./node_modules/@babel/runtime/helpers/asyncToGenerator.js");
/* harmony import */ var _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "./node_modules/@babel/runtime/helpers/slicedToArray.js");
/* harmony import */ var _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/regenerator */ "@babel/runtime/regenerator");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _components_affiliate_creative__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../components/affiliate-creative */ "./assets/js/editor/components/affiliate-creative.js");
/* harmony import */ var _components_icon__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../components/icon */ "./assets/js/editor/components/icon.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! @wordpress/url */ "@wordpress/url");
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_wordpress_url__WEBPACK_IMPORTED_MODULE_10__);




/**
 * Affiliate Creatives Component.
 *
 * @since 2.8
 */

/**
 * Internal dependencies
 */



/**
 * WordPress dependencies
 */







/**
 * Affiliate Creatives.
 *
 * Affiliate creatives component.
 *
 * @since 2.8
 *
 * @param {object}   attributes    Block attributes.
 * @param {function} setAttributes Method used to set the attributes for this component in the global scope.
 * @returns {JSX.Element}          The rendered component.
 */
function AffiliateCreatives(_ref) {
  var attributes = _ref.attributes,
    setAttributes = _ref.setAttributes;
  var _useState = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["useState"])([]),
    _useState2 = _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_1___default()(_useState, 2),
    creatives = _useState2[0],
    setCreatives = _useState2[1];
  var _useState3 = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["useState"])(),
    _useState4 = _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_1___default()(_useState3, 2),
    error = _useState4[0],
    setError = _useState4[1];
  var preview = attributes.preview,
    number = attributes.number;
  var CREATIVES_QUERY = {
    number: number,
    status: 'active'
  };
  Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["useEffect"])(function () {
    var ignore = false;
    function fetchData() {
      return _fetchData.apply(this, arguments);
    }
    function _fetchData() {
      _fetchData = _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_0___default()( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_3___default.a.mark(function _callee() {
        var result;
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_3___default.a.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                _context.prev = 0;
                _context.next = 3;
                return _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_9___default()({
                  path: Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_10__["addQueryArgs"])("/affwp/v1/creatives/", CREATIVES_QUERY)
                });
              case 3:
                result = _context.sent;
                if (!ignore) {
                  setCreatives(result);
                }
                _context.next = 10;
                break;
              case 7:
                _context.prev = 7;
                _context.t0 = _context["catch"](0);
                setError(_context.t0);
              case 10:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, null, [[0, 7]]);
      }));
      return _fetchData.apply(this, arguments);
    }
    fetchData();
    return function () {
      ignore = true;
    };
  }, [number]);
  var hasCreatives = Array.isArray(creatives) && creatives.length;
  var affiliateWpIcon = function affiliateWpIcon() {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["Icon"], {
      icon: _components_icon__WEBPACK_IMPORTED_MODULE_5__["default"],
      color: true
    });
  };
  var inspectorControls = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_8__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["PanelBody"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["ToggleControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__["__"])('Creative preview', 'affiliate-wp'),
    checked: !!preview,
    onChange: function onChange(value) {
      return setAttributes({
        preview: value
      });
    },
    help: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__["__"])('Displays an image or text preview above the HTML code.', 'affiliate-wp')
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__["__"])('Number', 'affiliate-wp'),
    type: "number",
    value: number,
    onChange: function onChange(number) {
      return setAttributes({
        number: number
      });
    },
    help: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__["__"])('The number of affiliate creatives to show.', 'affiliate-wp')
  })));
  if (!hasCreatives) {
    var ErrorMessage = function ErrorMessage() {
      if (error) {
        var message = error.message;
        return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["Fragment"], null, message);
      }
      return false;
    };
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["Fragment"], null, inspectorControls, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["Placeholder"], {
      icon: affiliateWpIcon,
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__["__"])('Affiliate Creatives', 'affiliate-wp')
    }, !Array.isArray(creatives) ? Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["Spinner"], null) : Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(ErrorMessage, null)));
  }
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["Fragment"], null, inspectorControls, creatives.map(function (creative) {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_components_affiliate_creative__WEBPACK_IMPORTED_MODULE_4__["default"], {
      key: creative.creative_id,
      id: creative.creative_id,
      name: creative.name,
      description: creative.description,
      image: creative.image,
      url: creative.url,
      text: creative.text,
      preview: preview
    });
  }));
}
/* harmony default export */ __webpack_exports__["default"] = (AffiliateCreatives);

/***/ }),

/***/ "./assets/js/editor/blocks/affiliate-creatives/index.js":
/*!**************************************************************!*\
  !*** ./assets/js/editor/blocks/affiliate-creatives/index.js ***!
  \**************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _components_icon__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../components/icon */ "./assets/js/editor/components/icon.js");
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit */ "./assets/js/editor/blocks/affiliate-creatives/edit.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/**
 * Affiliate Creatives URL Block.
 *
 * @since 2.8
 */

/**
 * Internal Dependencies
 */



/**
 * WordPress Dependencies
 */

var name = 'affiliatewp/affiliate-creatives';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Affiliate Creatives', 'affiliate-wp'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Show creatives to your affiliates.', 'affiliate-wp'),
  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Creative', 'affiliate-wp'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Banner', 'affiliate-wp')],
  category: 'affiliatewp',
  icon: _components_icon__WEBPACK_IMPORTED_MODULE_0__["default"],
  supports: {
    html: false
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_1__["default"],
  save: function save() {
    return null;
  }
};


/***/ }),

/***/ "./assets/js/editor/blocks/affiliate-referral-url/edit.js":
/*!****************************************************************!*\
  !*** ./assets/js/editor/blocks/affiliate-referral-url/edit.js ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_referral_url__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../utils/referral-url */ "./assets/js/editor/utils/referral-url.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__);

/**
 * Affiliate Referral URL Edit Component.
 *
 * @since 2.8
 */



/**
 * WordPress dependencies
 */





/**
 * Affiliate Referral URL.
 *
 * Affiliate referral URL component.
 *
 * @since 2.8
 *
 * @param {object}   attributes    Block attributes.
 * @param {function} setAttributes Method used to set the attributes for this component in the global scope.
 * @param {string}   className     The class name for the referral URL wrapper.
 * @returns {JSX.Element}          The rendered component.
 */
function AffiliateReferralUrl(_ref) {
  var attributes = _ref.attributes,
    setAttributes = _ref.setAttributes,
    className = _ref.className;
  var url = attributes.url,
    format = attributes.format,
    pretty = attributes.pretty;
  var referralUrl = Object(_utils_referral_url__WEBPACK_IMPORTED_MODULE_1__["default"])({
    url: url,
    format: format,
    pretty: pretty
  });
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_4__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["PanelBody"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["RadioControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Pretty Affiliate URLs', 'affiliate-wp'),
    selected: pretty,
    options: [{
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Site Default', 'affiliate-wp'),
      value: 'default'
    }, {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Yes', 'affiliate-wp'),
      value: 'yes'
    }, {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('No', 'affiliate-wp'),
      value: 'no'
    }],
    onChange: function onChange(option) {
      setAttributes({
        pretty: option
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["RadioControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Referral Format', 'affiliate-wp'),
    selected: format,
    options: [{
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Site Default', 'affiliate-wp'),
      value: 'default'
    }, {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('ID', 'affiliate-wp'),
      value: 'id'
    }, {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Username', 'affiliate-wp'),
      value: 'username'
    }],
    onChange: function onChange(option) {
      setAttributes({
        format: option
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["URLInput"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Custom URL', 'affiliate-wp'),
    className: 'components-text-control__input is-full-width',
    value: url,
    onChange: function onChange(url, post) {
      return setAttributes({
        url: url
      });
    },
    disableSuggestions: true,
    placeholder: ''
  }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("p", {
    className: className
  }, referralUrl));
}
/* harmony default export */ __webpack_exports__["default"] = (AffiliateReferralUrl);

/***/ }),

/***/ "./assets/js/editor/blocks/affiliate-referral-url/index.js":
/*!*****************************************************************!*\
  !*** ./assets/js/editor/blocks/affiliate-referral-url/index.js ***!
  \*****************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _components_icon__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../components/icon */ "./assets/js/editor/components/icon.js");
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit */ "./assets/js/editor/blocks/affiliate-referral-url/edit.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/**
 * Affiliate Referral URL Block.
 *
 * @since 2.8
 */

/**
 * Internal Dependencies
 */



/**
 * WordPress Dependencies
 */

var name = 'affiliatewp/affiliate-referral-url';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Affiliate Referral URL', 'affiliate-wp'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Display the referral URL of the currently logged in affiliate.', 'affiliate-wp'),
  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('URL', 'affiliate-wp'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Referral', 'affiliate-wp')],
  category: 'affiliatewp',
  icon: _components_icon__WEBPACK_IMPORTED_MODULE_0__["default"],
  supports: {
    html: false
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_1__["default"],
  save: function save() {
    return null;
  }
};


/***/ }),

/***/ "./assets/js/editor/blocks/login/edit.js":
/*!***********************************************!*\
  !*** ./assets/js/editor/blocks/login/edit.js ***!
  \***********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "./node_modules/@babel/runtime/helpers/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__);


function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default()(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
/**
 * Affiliate Login Edit Component.
 *
 * @since 2.8
 */

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */






/**
 * Affiliate Login.
 *
 * Affiliate Login Form Component.
 *
 * @since 2.8
 *
 * @param {object}   attributes    Block attributes.
 * @param {function} setAttributes Method used to set the attributes for this component in the global scope.
 * @returns {JSX.Element}          The rendered component.
 */
function AffiliateLogin(_ref) {
  var attributes = _ref.attributes,
    setAttributes = _ref.setAttributes,
    clientId = _ref.clientId;
  var redirect = attributes.redirect,
    legend = attributes.legend,
    label = attributes.label,
    buttonText = attributes.buttonText,
    placeholder = attributes.placeholder,
    placeholders = attributes.placeholders;
  var classes = classnames__WEBPACK_IMPORTED_MODULE_2___default()('affwp-button-login');
  var checkboxClasses = classnames__WEBPACK_IMPORTED_MODULE_2___default()('affwp-field', 'affwp-field-checkbox');
  var isStandaloneForm = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__["useSelect"])(function (select) {
    var _select = select(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__["store"]),
      getBlock = _select.getBlock,
      getBlockRootClientId = _select.getBlockRootClientId;
    var parentBlock = getBlock(getBlockRootClientId(clientId));
    return 'affiliatewp/affiliate-area' !== (parentBlock === null || parentBlock === void 0 ? void 0 : parentBlock.name);
  }, [clientId]);

  // Clear any redirect if login block is within Affiliate Area block.
  Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["useEffect"])(function () {
    if (false === isStandaloneForm) {
      setAttributes({
        redirect: undefined
      });
    }
  }, [clientId]);
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('General', 'affiliate-wp')
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Redirect', 'affiliate-wp'),
    value: redirect,
    onChange: function onChange(redirect) {
      return setAttributes({
        redirect: redirect
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Form Title', 'affiliate-wp'),
    value: legend || Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Log into your account', 'affiliate-wp'),
    onChange: function onChange(legend) {
      return setAttributes({
        legend: legend
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Button Text', 'affiliate-wp'),
    value: buttonText,
    onChange: function onChange(buttonText) {
      return setAttributes({
        buttonText: buttonText
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["ToggleControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Show Placeholder Text', 'affiliate-wp'),
    checked: placeholders,
    onChange: function onChange(boolean) {
      return setAttributes({
        placeholders: boolean
      });
    }
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Field Labels', 'affiliate-wp'),
    initialOpen: false
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Username', 'affiliate-wp'),
    value: label === null || label === void 0 ? void 0 : label.username,
    onChange: function onChange(value) {
      setAttributes({
        label: _objectSpread(_objectSpread({}, label), {}, {
          username: value
        })
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Password', 'affiliate-wp'),
    value: label === null || label === void 0 ? void 0 : label.password,
    onChange: function onChange(value) {
      setAttributes({
        label: _objectSpread(_objectSpread({}, label), {}, {
          password: value
        })
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Remember Text', 'affiliate-wp'),
    value: label === null || label === void 0 ? void 0 : label.userRemember,
    onChange: function onChange(value) {
      setAttributes({
        label: _objectSpread(_objectSpread({}, label), {}, {
          userRemember: value
        })
      });
    }
  })), placeholders && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Field Placeholders', 'affiliate-wp'),
    initialOpen: false
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Username', 'affiliate-wp'),
    value: placeholder === null || placeholder === void 0 ? void 0 : placeholder.username,
    onChange: function onChange(value) {
      setAttributes({
        placeholder: _objectSpread(_objectSpread({}, placeholder), {}, {
          username: value
        })
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Password', 'affiliate-wp'),
    value: placeholder === null || placeholder === void 0 ? void 0 : placeholder.password,
    onChange: function onChange(value) {
      setAttributes({
        placeholder: _objectSpread(_objectSpread({}, placeholder), {}, {
          password: value
        })
      });
    }
  }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
    id: "affwp-login-form",
    className: "affwp-form"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__["RichText"], {
    identifier: 'legend',
    tagName: "h3",
    value: legend || Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Log into your account', 'affiliate-wp'),
    onChange: function onChange(legend) {
      setAttributes({
        legend: legend
      });
    },
    allowedFormats: []
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
    className: 'wp-block block-editor-block-list__block'
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
    className: "affwp-field-label"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__["RichText"], {
    identifier: 'labelUsername',
    tagName: "label",
    value: label === null || label === void 0 ? void 0 : label.username,
    onChange: function onChange(value) {
      setAttributes({
        label: _objectSpread(_objectSpread({}, label), {}, {
          username: value
        })
      });
    },
    placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Add label…', 'affiliate-wp'),
    allowedFormats: []
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__["RichText"], {
    identifier: "fieldUsername",
    placeholder: placeholders ? Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Add placeholder text…', 'affiliate-wp') : '',
    value: placeholders ? placeholder === null || placeholder === void 0 ? void 0 : placeholder.username : '',
    onChange: function onChange(value) {
      setAttributes({
        placeholder: _objectSpread(_objectSpread({}, placeholder), {}, {
          username: value
        })
      });
    },
    allowedFormats: [],
    type: 'text',
    className: 'affwp-field affwp-field-text'
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
    className: 'wp-block block-editor-block-list__block'
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
    className: "affwp-field-label"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__["RichText"], {
    identifier: 'labelPassword',
    tagName: "label",
    value: label === null || label === void 0 ? void 0 : label.password,
    onChange: function onChange(value) {
      setAttributes({
        label: _objectSpread(_objectSpread({}, label), {}, {
          password: value
        })
      });
    },
    placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Add label…', 'affiliate-wp'),
    allowedFormats: []
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__["RichText"], {
    identifier: "fieldPassword",
    placeholder: placeholders ? Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Add placeholder text…', 'affiliate-wp') : '',
    value: placeholders ? placeholder === null || placeholder === void 0 ? void 0 : placeholder.password : '',
    onChange: function onChange(value) {
      setAttributes({
        placeholder: _objectSpread(_objectSpread({}, placeholder), {}, {
          password: value
        })
      });
    },
    allowedFormats: [],
    type: 'text',
    className: 'affwp-field affwp-field-text'
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
    className: 'wp-block block-editor-block-list__block'
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("input", {
    className: checkboxClasses,
    type: "checkbox"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__["RichText"], {
    identifier: 'labelUserRemember',
    tagName: "label",
    value: label === null || label === void 0 ? void 0 : label.userRemember,
    onChange: function onChange(value) {
      setAttributes({
        label: _objectSpread(_objectSpread({}, label), {}, {
          userRemember: value
        })
      });
    },
    placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Add label…', 'affiliate-wp')
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
    className: 'wp-block block-editor-block-list__block'
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_5__["RichText"], {
    identifier: "loginButton",
    placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Add button text…', 'affiliate-wp'),
    value: buttonText,
    onChange: function onChange(buttonText) {
      return setAttributes({
        buttonText: buttonText
      });
    },
    withoutInteractiveFormatting: true,
    allowedFormats: [],
    className: classes
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("p", {
    className: "affwp-lost-password"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("a", null, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Lost your password?', 'affiliate-wp')))));
}
/* harmony default export */ __webpack_exports__["default"] = (AffiliateLogin);

/***/ }),

/***/ "./assets/js/editor/blocks/login/index.js":
/*!************************************************!*\
  !*** ./assets/js/editor/blocks/login/index.js ***!
  \************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _components_icon__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../components/icon */ "./assets/js/editor/components/icon.js");
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit */ "./assets/js/editor/blocks/login/edit.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/**
 * Affiliate Login Block.
 *
 * @since 2.8
 */

/**
 * Internal Dependencies
 */



/**
 * WordPress Dependencies
 */

var name = 'affiliatewp/login';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Affiliate Login', 'affiliate-wp'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Allow your affiliates to login.', 'affiliate-wp'),
  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Login', 'affiliate-wp'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Form', 'affiliate-wp')],
  category: 'affiliatewp',
  icon: _components_icon__WEBPACK_IMPORTED_MODULE_0__["default"],
  supports: {
    html: false
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_1__["default"],
  save: function save() {
    return null;
  }
};


/***/ }),

/***/ "./assets/js/editor/blocks/non-affiliate-content/edit.js":
/*!***************************************************************!*\
  !*** ./assets/js/editor/blocks/non-affiliate-content/edit.js ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return NonAffiliateContentEdit; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _components_icon__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../components/icon */ "./assets/js/editor/components/icon.js");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__);

/**
 * Non-Affiliate Content Edit Component.
 *
 * @since 2.8
 */

/**
 * Internal dependencies
 */


/**
 * WordPress dependencies
 */




function NonAffiliateContentEdit(_ref) {
  var className = _ref.className;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
    className: className
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__["InnerBlocks"], null));
}
var withNotice = Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_4__["createHigherOrderComponent"])(function (BlockListBlock) {
  return function (props) {
    if (props.isSelected) {
      // Get ID of parent block.
      var parentBlockId = wp.data.select('core/block-editor').getBlockHierarchyRootClientId(props.clientId);
      if (parentBlockId && props.name !== 'affiliatewp/non-affiliate-content') {
        // Get parent block.
        var parentBlock = wp.data.select('core/block-editor').getBlock(parentBlockId);

        // If the parent block is the affiliate content block, show a message.
        if ('affiliatewp/non-affiliate-content' === parentBlock.name) {
          return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(BlockListBlock, props), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["Notice"], {
            isDismissible: false
          }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["Icon"], {
            icon: _components_icon__WEBPACK_IMPORTED_MODULE_1__["default"],
            color: true
          }), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('This block will only be shown to non affiliates', 'affiliate-wp')));
        }
      }
    }
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(BlockListBlock, props);
  };
}, 'withNotice');
wp.hooks.addFilter('editor.BlockListBlock', 'affiliate-wp/with-notice', withNotice);

/***/ }),

/***/ "./assets/js/editor/blocks/non-affiliate-content/index.js":
/*!****************************************************************!*\
  !*** ./assets/js/editor/blocks/non-affiliate-content/index.js ***!
  \****************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _components_icon__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../components/icon */ "./assets/js/editor/components/icon.js");
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./edit */ "./assets/js/editor/blocks/non-affiliate-content/edit.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__);

/**
 * Non-Affiliate Content Block.
 *
 * @since 2.8
 */

/**
 * Internal dependencies
 */



/**
 * WordPress dependencies
 */


var name = 'affiliatewp/non-affiliate-content';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Non Affiliate Content', 'affiliate-wp'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Show content to non affiliates.', 'affiliate-wp'),
  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Content', 'affiliate-wp'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Restrict', 'affiliate-wp')],
  category: 'affiliatewp',
  icon: _components_icon__WEBPACK_IMPORTED_MODULE_1__["default"],
  supports: {
    html: false
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_2__["default"],
  save: function save(_ref) {
    var className = _ref.className;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      className: className
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["InnerBlocks"].Content, null));
  }
};


/***/ }),

/***/ "./assets/js/editor/blocks/opt-in/edit.js":
/*!************************************************!*\
  !*** ./assets/js/editor/blocks/opt-in/edit.js ***!
  \************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/editor */ "@wordpress/editor");
/* harmony import */ var _wordpress_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);

/**
 * Opt-In Form Edit Component.
 *
 * @since 2.8
 */

/**
 * External dependencies
 */




/**
 * Opt-In Form.
 *
 * Affiliate registration opt-in form.
 *
 * @since 2.8
 *
 * @param {object}   attributes    Block attributes.
 * @param {function} setAttributes Method used to set the attributes for this component in the global scope.
 * @returns {JSX.Element}          The rendered component.
 */
function OptInForm(_ref) {
  var attributes = _ref.attributes,
    setAttributes = _ref.setAttributes;
  var redirect = attributes.redirect;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_editor__WEBPACK_IMPORTED_MODULE_2__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["PanelBody"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Redirect'),
    value: redirect,
    onChange: function onChange(redirect) {
      return setAttributes({
        redirect: redirect
      });
    }
  }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
    id: "affwp-login-form",
    className: "affwp-form"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("p", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("label", {
    htmlFor: "affwp-opt-in-name"
  }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('First Name', 'affiliate-wp')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("input", {
    id: "affwp-opt-in-name",
    className: "required",
    type: "text",
    name: "affwp_first_name",
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('First Name', 'affiliate-wp')
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("p", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("label", {
    htmlFor: "affwp-opt-in-name"
  }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Last Name', 'affiliate-wp')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("input", {
    id: "affwp-opt-in-name",
    className: "required",
    type: "text",
    name: "affwp_last_name",
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Last Name', 'affiliate-wp')
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("p", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("label", {
    htmlFor: "affwp-opt-in-email"
  }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Email Address', 'affiliate-wp')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("input", {
    id: "affwp-opt-in-email",
    className: "required",
    type: "text",
    name: "affwp_email",
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Email Address', 'affiliate-wp')
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("p", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("input", {
    className: "button",
    type: "submit",
    value: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Subscribe', 'affiliate-wp')
  }))));
}
/* harmony default export */ __webpack_exports__["default"] = (OptInForm);

/***/ }),

/***/ "./assets/js/editor/blocks/opt-in/index.js":
/*!*************************************************!*\
  !*** ./assets/js/editor/blocks/opt-in/index.js ***!
  \*************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _components_icon__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../components/icon */ "./assets/js/editor/components/icon.js");
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit */ "./assets/js/editor/blocks/opt-in/edit.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/**
 * Opt-In Form Block.
 *
 * @since 2.8
 */

/**
 * Internal Dependencies
 */



/**
 * WordPress Dependencies
 */

var name = 'affiliatewp/opt-in';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Opt-in Form', 'affiliate-wp'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Show an opt-in form that integrates with Mailchimp, ActiveCampaign, or ConvertKit.', 'affiliate-wp'),
  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Opt-in', 'affiliate-wp'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Form', 'affiliate-wp'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Sign Up', 'affiliate-wp')],
  category: 'affiliatewp',
  icon: _components_icon__WEBPACK_IMPORTED_MODULE_0__["default"],
  supports: {
    html: false
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_1__["default"],
  save: function save() {
    return null;
  }
};


/***/ }),

/***/ "./assets/js/editor/blocks/registration/components/email-edit.js":
/*!***********************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/components/email-edit.js ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _field__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./field */ "./assets/js/editor/blocks/registration/components/field.js");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_5__);

/**
 * Affiliate registration form email field edit component.
 *
 * @since 2.8
 */






function EmailEdit(props) {
  var _props$attributes = props.attributes,
    required = _props$attributes.required,
    label = _props$attributes.label,
    classNames = _props$attributes.classNames,
    placeholder = _props$attributes.placeholder,
    type = _props$attributes.type;
  var fieldClassNames = classnames__WEBPACK_IMPORTED_MODULE_5___default()('affwp-field', 'affwp-field-text');
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["_x"])('Field settings', 'Email field', 'affiliate-wp'),
    initialOpen: true
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["ToggleControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["_x"])('Required', 'Email field', 'affiliate-wp'),
    className: "affwp-field-label__required",
    checked: required,
    disabled: props.disableRequired || false,
    help: props.help || '',
    onChange: function onChange(required) {
      return props.setAttributes({
        required: required
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["_x"])('Field Label', 'Email field', 'affiliate-wp'),
    value: label,
    onChange: function onChange(label) {
      return props.setAttributes({
        label: label
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["_x"])('Field Placeholder', 'Email field', 'affiliate-wp'),
    value: placeholder,
    onChange: function onChange(placeholder) {
      return props.setAttributes({
        placeholder: placeholder
      });
    }
  }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_field__WEBPACK_IMPORTED_MODULE_2__["default"], {
    label: label,
    type: "email",
    required: required,
    setAttributes: props.setAttributes,
    isSelected: props.isSelected,
    name: props.name,
    classNames: classNames,
    fieldClassNames: fieldClassNames,
    placeholder: placeholder,
    context: props.context
  }));
}
/* harmony default export */ __webpack_exports__["default"] = (EmailEdit);

/***/ }),

/***/ "./assets/js/editor/blocks/registration/components/field-controls.js":
/*!***************************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/components/field-controls.js ***!
  \***************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




var AffiliateWPFieldControls = function AffiliateWPFieldControls(_ref) {
  var setAttributes = _ref.setAttributes,
    width = _ref.width,
    id = _ref.id,
    required = _ref.required;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Field Settings', 'affiliate-wp')
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["ToggleControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Field is required', 'affiliate-wp'),
    className: "affiliatewp-field-label__required",
    checked: required,
    onChange: function onChange(value) {
      return setAttributes({
        required: value
      });
    },
    help: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Does this field have to be completed for the form to be submitted?', 'affiliate-wp')
  }))));
};
/* harmony default export */ __webpack_exports__["default"] = (AffiliateWPFieldControls);

/***/ }),

/***/ "./assets/js/editor/blocks/registration/components/field-label.js":
/*!************************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/components/field-label.js ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "./node_modules/@babel/runtime/helpers/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__);


/**
 * Affiliate registration field label component
 *
 * @since 2.8
 */

/**
 * External dependencies
 */


var AffiliateWPFieldLabel = function AffiliateWPFieldLabel(_ref) {
  var setAttributes = _ref.setAttributes,
    label = _ref.label,
    labelFieldName = _ref.labelFieldName,
    placeholder = _ref.placeholder,
    resetFocus = _ref.resetFocus,
    required = _ref.required;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
    className: "affwp-field-label"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["RichText"], {
    tagName: "label",
    value: label,
    onChange: function onChange(value) {
      if (resetFocus) {
        resetFocus();
      }
      if (labelFieldName) {
        setAttributes(_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default()({}, labelFieldName, value));
        return;
      }
      setAttributes({
        label: value
      });
    },
    placeholder: placeholder !== null && placeholder !== void 0 ? placeholder : Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Add label…', 'affiliate-wp'),
    withoutInteractiveFormatting: true,
    allowedFormats: []
  }), required && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("span", {
    className: "required"
  }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('(required)', 'affiliate-wp')));
};
/* harmony default export */ __webpack_exports__["default"] = (AffiliateWPFieldLabel);

/***/ }),

/***/ "./assets/js/editor/blocks/registration/components/field-multiple.js":
/*!***************************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/components/field-multiple.js ***!
  \***************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_typeof__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "./node_modules/@babel/runtime/helpers/typeof.js");
/* harmony import */ var _babel_runtime_helpers_typeof__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_typeof__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "./node_modules/@babel/runtime/helpers/slicedToArray.js");
/* harmony import */ var _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _field_controls__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./field-controls */ "./assets/js/editor/blocks/registration/components/field-controls.js");
/* harmony import */ var _field_label__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./field-label */ "./assets/js/editor/blocks/registration/components/field-label.js");
/* harmony import */ var _option__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./option */ "./assets/js/editor/blocks/registration/components/option.js");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_9__);



/**
 * Internal dependencies
 */




/**
 * WordPress dependencies
 */





function AffiliateWPFieldMultiple(props) {
  var id = props.id,
    type = props.type,
    instanceId = props.instanceId,
    required = props.required,
    label = props.label,
    setAttributes = props.setAttributes,
    isSelected = props.isSelected,
    options = props.options;
  var _useState = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["useState"])(null),
    _useState2 = _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_1___default()(_useState, 2),
    inFocus = _useState2[0],
    setInFocus = _useState2[1];
  var onChangeOption = function onChangeOption() {
    var key = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
    var option = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    var newOptions = options.slice(0);
    if (null === option) {
      // Remove a key
      newOptions.splice(key, 1);
      if (key > 0) {
        setInFocus(key - 1);
      }
    } else {
      // update a key
      newOptions.splice(key, 1, option);
      setInFocus(key); // set the focus.
    }

    setAttributes({
      options: newOptions
    });
  };
  var addNewOption = function addNewOption() {
    var key = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
    var newOptions = options.slice(0);
    var newInFocus = 0;
    if ('object' === _babel_runtime_helpers_typeof__WEBPACK_IMPORTED_MODULE_0___default()(key)) {
      newOptions.push('');
      newInFocus = newOptions.length - 1;
    } else {
      newOptions.splice(key + 1, 0, '');
      newInFocus = key + 1;
    }
    setInFocus(newInFocus);
    setAttributes({
      options: newOptions
    });
  };
  var blockProps = Object(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_9__["useBlockProps"])();
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("div", blockProps, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_field_label__WEBPACK_IMPORTED_MODULE_4__["default"], {
    required: required,
    label: label,
    setAttributes: setAttributes,
    isSelected: isSelected,
    resetFocus: function resetFocus() {
      return setInFocus(null);
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])("ol", {
    className: "affiliatewp-field-multiple__list",
    id: "affiliatewp-field-multiple-".concat(instanceId)
  }, options.map(function (option, index) {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_option__WEBPACK_IMPORTED_MODULE_5__["default"], {
      type: type,
      key: index,
      option: option,
      index: index,
      onChangeOption: onChangeOption,
      onAddOption: addNewOption,
      isInFocus: index === inFocus && isSelected,
      isSelected: isSelected
    });
  })), isSelected && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_6__["Button"], {
    className: "affiliatewp-field-multiple__add-option",
    icon: "insert",
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__["__"])('Insert option', 'affiliate-wp'),
    onClick: addNewOption
  }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__["__"])('Add option', 'affiliate-wp')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__["createElement"])(_field_controls__WEBPACK_IMPORTED_MODULE_3__["default"], {
    id: id,
    required: required,
    setAttributes: setAttributes
  })));
}
/* harmony default export */ __webpack_exports__["default"] = (Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_7__["withInstanceId"])(AffiliateWPFieldMultiple));

/***/ }),

/***/ "./assets/js/editor/blocks/registration/components/field.js":
/*!******************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/components/field.js ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _field_label__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./field-label */ "./assets/js/editor/blocks/registration/components/field-label.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__);

/**
 * Affiliate registration field component.
 *
 * @since 2.8
 */

/**
 * External dependencies
 */


/**
 * Internal dependencies
 */


/**
 * WordPress dependencies
 */


function AffiliateWPField(_ref) {
  var isSelected = _ref.isSelected,
    required = _ref.required,
    requiredAttribute = _ref.requiredAttribute,
    label = _ref.label,
    setAttributes = _ref.setAttributes,
    name = _ref.name,
    type = _ref.type,
    classNames = _ref.classNames,
    fieldClassNames = _ref.fieldClassNames,
    placeholder = _ref.placeholder,
    context = _ref.context;
  var blockProps = Object(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["useBlockProps"])();
  var showPlaceholders = context['affiliatewp/placeholders'];
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", blockProps, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_field_label__WEBPACK_IMPORTED_MODULE_2__["default"], {
    identifier: "label",
    required: required,
    requiredAttribute: requiredAttribute,
    label: label,
    labelAttribute: 'label',
    setAttributes: setAttributes,
    isSelected: isSelected,
    name: name,
    context: context
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["RichText"], {
    identifier: "placeholder",
    placeholder: showPlaceholders ? Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Add placeholder text…') : '',
    value: placeholder,
    onChange: function onChange(placeholder) {
      return setAttributes({
        placeholder: placeholder
      });
    },
    allowedFormats: [],
    type: type,
    className: fieldClassNames
  })));
}
/* harmony default export */ __webpack_exports__["default"] = (AffiliateWPField);

/***/ }),

/***/ "./assets/js/editor/blocks/registration/components/option.js":
/*!*******************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/components/option.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "./node_modules/@babel/runtime/helpers/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/createClass */ "./node_modules/@babel/runtime/helpers/createClass.js");
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/assertThisInitialized */ "./node_modules/@babel/runtime/helpers/assertThisInitialized.js");
/* harmony import */ var _babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/inherits */ "./node_modules/@babel/runtime/helpers/inherits.js");
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__);







function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_5___default()(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_5___default()(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_4___default()(this, result); }; }
function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }



var AffiliateWPOption = /*#__PURE__*/function (_Component) {
  _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_3___default()(AffiliateWPOption, _Component);
  var _super = _createSuper(AffiliateWPOption);
  function AffiliateWPOption() {
    var _this;
    _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default()(this, AffiliateWPOption);
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }
    _this = _super.call.apply(_super, [this].concat(args));
    _this.onChangeOption = _this.onChangeOption.bind(_babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_2___default()(_this));
    _this.onKeyPress = _this.onKeyPress.bind(_babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_2___default()(_this));
    _this.onDeleteOption = _this.onDeleteOption.bind(_babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_2___default()(_this));
    _this.textInput = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createRef"])();
    return _this;
  }
  _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default()(AffiliateWPOption, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      if (this.props.isInFocus) {
        this.textInput.current.focus();
      }
    }
  }, {
    key: "componentDidUpdate",
    value: function componentDidUpdate() {
      if (this.props.isInFocus) {
        this.textInput.current.focus();
      }
    }
  }, {
    key: "onChangeOption",
    value: function onChangeOption(event) {
      this.props.onChangeOption(this.props.index, event.target.value);
    }
  }, {
    key: "onKeyPress",
    value: function onKeyPress(event) {
      if (event.key === 'Enter') {
        this.props.onAddOption(this.props.index);
        event.preventDefault();
        return;
      }
      if (event.key === 'Backspace' && event.target.value === '') {
        this.props.onChangeOption(this.props.index);
        event.preventDefault();
      }
    }
  }, {
    key: "onDeleteOption",
    value: function onDeleteOption() {
      this.props.onChangeOption(this.props.index);
    }
  }, {
    key: "render",
    value: function render() {
      var _this$props = this.props,
        isSelected = _this$props.isSelected,
        option = _this$props.option,
        type = _this$props.type;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("li", {
        className: "affiliatewp-option"
      }, type && type !== 'select' && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("input", {
        className: "affiliatewp-option__type",
        type: type,
        disabled: true
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])("input", {
        type: "text",
        className: "affiliatewp-option__input",
        value: option,
        placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__["__"])('Write option…', 'affiliate-wp'),
        onChange: this.onChangeOption,
        onKeyDown: this.onKeyPress,
        ref: this.textInput
      }), isSelected && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["Button"], {
        className: "affiliatewp-option__remove",
        icon: "trash",
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_8__["__"])('Remove option', 'affiliate-wp'),
        onClick: this.onDeleteOption
      }));
    }
  }]);
  return AffiliateWPOption;
}(_wordpress_element__WEBPACK_IMPORTED_MODULE_6__["Component"]);
/* harmony default export */ __webpack_exports__["default"] = (AffiliateWPOption);

/***/ }),

/***/ "./assets/js/editor/blocks/registration/components/text-edit.js":
/*!**********************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/components/text-edit.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _field__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./field */ "./assets/js/editor/blocks/registration/components/field.js");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_5__);

/**
 * Affiliate registration form text field edit component.
 *
 * @since 2.8
 */






function TextEdit(props) {
  var _props$attributes = props.attributes,
    required = _props$attributes.required,
    label = _props$attributes.label,
    classNames = _props$attributes.classNames,
    placeholder = _props$attributes.placeholder,
    type = _props$attributes.type;
  var fieldClassNames = classnames__WEBPACK_IMPORTED_MODULE_5___default()('affwp-field', 'affwp-field-text');
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["_x"])('Field settings', 'Text field', 'affiliate-wp'),
    initialOpen: true
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["ToggleControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["_x"])('Required', 'Text field', 'affiliate-wp'),
    className: "affwp-field-label__required",
    checked: required,
    onChange: function onChange(required) {
      return props.setAttributes({
        required: required
      });
    },
    disabled: props.disableRequired || false,
    help: props.help || ''
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["_x"])('Field Label', 'Text field', 'affiliate-wp'),
    value: label,
    onChange: function onChange(label) {
      return props.setAttributes({
        label: label
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["_x"])('Field Placeholder', 'Text field', 'affiliate-wp'),
    value: placeholder,
    onChange: function onChange(placeholder) {
      return props.setAttributes({
        placeholder: placeholder
      });
    }
  }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_field__WEBPACK_IMPORTED_MODULE_2__["default"], {
    label: label,
    type: "text",
    required: required,
    setAttributes: props.setAttributes,
    isSelected: props.isSelected,
    name: props.name,
    classNames: classNames,
    fieldClassNames: fieldClassNames,
    placeholder: placeholder,
    context: props.context
  }));
}
/* harmony default export */ __webpack_exports__["default"] = (TextEdit);

/***/ }),

/***/ "./assets/js/editor/blocks/registration/edit.js":
/*!******************************************************!*\
  !*** ./assets/js/editor/blocks/registration/edit.js ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _fields_username__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./fields/username */ "./assets/js/editor/blocks/registration/fields/username/index.js");

/**
 * Registration Edit Component.
 *
 * @since 2.8
 */

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */






var ALLOWED_BLOCKS = ['affiliatewp/field-email', 'affiliatewp/field-text', 'affiliatewp/field-textarea', 'affiliatewp/field-website', 'affiliatewp/field-checkbox', 'affiliatewp/field-password', 'affiliatewp/field-phone', 'affiliatewp/field-register-button'];
var hasTermsOfUse = affwp_blocks.terms_of_use;
var termsOfUseLink = affwp_blocks.terms_of_use_link;
var template = [['affiliatewp/field-name', {
  label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Your Name', 'affiliate-wp'),
  type: 'name'
}], ['affiliatewp/field-username', {
  label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Username', 'affiliate-wp'),
  required: true,
  type: 'username'
}], ['affiliatewp/field-account-email', {
  label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Account Email', 'affiliate-wp'),
  required: true,
  type: 'account'
}], ['affiliatewp/field-payment-email', {
  label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Payment Email', 'affiliate-wp'),
  type: 'payment'
}], ['affiliatewp/field-website', {
  label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Website URL', 'affiliate-wp'),
  type: 'websiteUrl'
}], ['affiliatewp/field-textarea', {
  label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('How will you promote us?', 'affiliate-wp'),
  type: 'promotionMethod'
}]];
if (hasTermsOfUse) {
  template.push(['affiliatewp/field-terms-of-use', {
    label: "Agree to our <a href=\"".concat(termsOfUseLink, "\" target=\"_blank\">Terms of Use and Privacy Policy</a>"),
    required: true
  }]);
}
template.push(['affiliatewp/field-register-button']);

/**
 * Affiliate Registration.
 *
 * Affiliate registration edit block.
 *
 * @since 2.8
 *
 * @param {object}   attributes    Block attributes.
 * @param {function} setAttributes Method used to set the attributes for this component in the global scope.
 * @returns {JSX.Element}          The rendered component.
 */
function AffiliateRegistration(_ref) {
  var name = _ref.name,
    attributes = _ref.attributes,
    setAttributes = _ref.setAttributes,
    isSelected = _ref.isSelected,
    context = _ref.context,
    clientId = _ref.clientId;
  var redirect = attributes.redirect,
    placeholders = attributes.placeholders,
    legend = attributes.legend;
  var allowAffiliateRegistration = affwp_blocks.allow_affiliate_registration;
  var classes = classnames__WEBPACK_IMPORTED_MODULE_1___default()('affwp-form');
  var blockProps = Object(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["useBlockProps"])({
    className: classes
  });
  var useInnerBlocksProps = _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["useInnerBlocksProps"] ? _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["useInnerBlocksProps"] : _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["__experimentalUseInnerBlocksProps"];
  var innerBlocksProps = useInnerBlocksProps(blockProps, {
    template: template,
    allowedBlocks: ALLOWED_BLOCKS
  });
  var isStandaloneForm = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__["useSelect"])(function (select) {
    var _select = select(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["store"]),
      getBlock = _select.getBlock,
      getBlockRootClientId = _select.getBlockRootClientId;
    var parentBlock = getBlock(getBlockRootClientId(clientId));
    return 'affiliatewp/affiliate-area' !== (parentBlock === null || parentBlock === void 0 ? void 0 : parentBlock.name);
  }, [clientId]);

  // Clear any redirect if registration block is within Affiliate Area block.
  Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["useEffect"])(function () {
    if (false === isStandaloneForm) {
      setAttributes({
        redirect: undefined
      });
    }
  }, [clientId]);
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('General', 'affiliate-wp')
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Form Title', 'affiliate-wp'),
    value: legend || Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Register a new affiliate account', 'affiliate-wp'),
    onChange: function onChange(legend) {
      return setAttributes({
        legend: legend
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["ToggleControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Show Placeholder Text', 'affiliate-wp'),
    checked: placeholders,
    onChange: function onChange(boolean) {
      return setAttributes({
        placeholders: boolean
      });
    }
  })), !allowAffiliateRegistration && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["Notice"], {
    className: "affwp-block-inspector-notice",
    isDismissible: false,
    status: "warning"
  }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Affiliates will not see this form as "Allow Affiliate Registration" is disabled.', 'affiliate-wp')), true === isStandaloneForm && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["PanelBody"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Redirect'),
    value: redirect,
    onChange: function onChange(redirect) {
      return setAttributes({
        redirect: redirect
      });
    }
  }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", blockProps, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["RichText"], {
    identifier: 'legend',
    tagName: "h3",
    value: legend || Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Register a new affiliate account', 'affiliate-wp'),
    onChange: function onChange(legend) {
      setAttributes({
        legend: legend
      });
    },
    allowedFormats: []
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", innerBlocksProps)));
}
/* harmony default export */ __webpack_exports__["default"] = (AffiliateRegistration);

/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/account-email/edit.js":
/*!***************************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/account-email/edit.js ***!
  \***************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_email_edit__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../components/email-edit */ "./assets/js/editor/blocks/registration/components/email-edit.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__);

/**
 * Affiliate Registration Form Email field Edit Component.
 *
 * @since 2.8
 */

/**
 * External dependencies
 */


/**
 * Internal dependencies
 */


/**
 * WordPress dependencies
 */




function AffiliateWPFieldEmail(_ref) {
  var attributes = _ref.attributes,
    setAttributes = _ref.setAttributes,
    isSelected = _ref.isSelected,
    name = _ref.name,
    context = _ref.context,
    clientId = _ref.clientId;
  var required = attributes.required,
    label = attributes.label,
    classNames = attributes.classNames,
    placeholder = attributes.placeholder,
    type = attributes.type;
  var fieldClassNames = classnames__WEBPACK_IMPORTED_MODULE_1___default()('affwp-field', 'affwp-field-email');
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_components_email_edit__WEBPACK_IMPORTED_MODULE_2__["default"], {
    attributes: attributes,
    disableRequired: true,
    help: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('The Account Email field is always required', 'affiliate-wp'),
    setAttributes: setAttributes,
    isSelected: isSelected,
    name: name,
    context: context,
    clientId: clientId
  });
}
/* harmony default export */ __webpack_exports__["default"] = (AffiliateWPFieldEmail);

/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/account-email/index.js":
/*!****************************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/account-email/index.js ***!
  \****************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit */ "./assets/js/editor/blocks/registration/fields/account-email/edit.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);

/**
 * Affiliate registration email field Block.
 *
 * @since 2.8
 */

/**
 * Internal dependencies
 */


/**
 * WordPress dependencies
 */


var icon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Icon"], {
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    className: "h-6 w-6",
    fill: "none",
    viewBox: "0 0 24 24",
    stroke: "currentColor"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: 2,
    d: "M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207",
    fill: "none"
  }))
});
var name = 'affiliatewp/field-account-email';
var settings = {
  /* translators: block name */
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Account Email', 'affiliate-wp'),
  category: 'affiliatewp',
  parent: ['affiliatewp/registration'],
  icon: icon,
  attributes: {
    label: {
      type: 'string',
      default: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Account Email', 'affiliate-wp')
    },
    required: {
      type: 'boolean',
      default: true
    },
    placeholder: {
      type: 'string'
    },
    type: {
      type: 'string'
    }
  },
  /* translators: block description */
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('A field for collecting the account email address.', 'affiliate-wp'),
  keywords: ['affiliatewp', /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('e-mail', 'affiliate-wp'), /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('mail', 'affiliate-wp'), /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('account', 'affiliate-wp')],
  supports: {
    html: false,
    lightBlockWrapper: true
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_1__["default"],
  save: function save() {
    return null;
  }
};


/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/checkbox-multiple/index.js":
/*!********************************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/checkbox-multiple/index.js ***!
  \********************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _components_field_multiple__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../components/field-multiple */ "./assets/js/editor/blocks/registration/components/field-multiple.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__);

/**
 * Affiliate registration Checkbox (multiple) field Block.
 *
 * @since 2.10.0
 */



/**
 * WordPress dependencies
 */



var icon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Icon"], {
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24",
    height: 24,
    width: 24
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("defs", null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("title", null, "checklist"), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("rect", {
    x: 0.75,
    y: 0.749,
    width: 22.5,
    height: 22.5,
    rx: 1.5,
    ry: 1.5,
    fill: "none",
    stroke: "#000000",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: "2px"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("polyline", {
    points: "12 4.499 7.5 10.499 4.5 7.499",
    fill: "none",
    stroke: "#000000",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: "2px"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("line", {
    x1: 14.25,
    y1: 8.249,
    x2: 18.75,
    y2: 8.249,
    fill: "none",
    stroke: "#000000",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: "2px"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("polyline", {
    points: "12 13.499 7.5 19.499 4.5 16.499",
    fill: "none",
    stroke: "#000000",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: "2px"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("line", {
    x1: 14.25,
    y1: 17.249,
    x2: 18.75,
    y2: 17.249,
    fill: "none",
    stroke: "#000000",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: "2px"
  }))
});
var name = 'affiliatewp/field-checkbox-multiple';
var getFieldLabel = function getFieldLabel(_ref) {
  var attributes = _ref.attributes,
    blockName = _ref.name;
  return null === attributes.label ? Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["getBlockType"])(blockName).title : attributes.label;
};
var editMultiField = function editMultiField(type) {
  return function (props) {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_components_field_multiple__WEBPACK_IMPORTED_MODULE_1__["default"], {
      label: getFieldLabel(props),
      required: props.attributes.required,
      options: props.attributes.options,
      setAttributes: props.setAttributes,
      type: type,
      isSelected: props.isSelected,
      id: props.attributes.id
    });
  };
};
var settings = {
  apiVersion: 2,
  /* translators: block name */
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Multiple Choice (Checkbox)', 'affiliate-wp'),
  category: 'affiliatewp',
  parent: ['affiliatewp/registration'],
  icon: icon,
  attributes: {
    label: {
      type: 'string',
      default: 'Choose several options'
    },
    required: {
      type: 'boolean',
      default: false
    },
    options: {
      type: 'array',
      default: []
    },
    defaultValue: {
      type: 'string',
      default: ''
    },
    placeholder: {
      type: 'string',
      default: ''
    },
    id: {
      type: 'string',
      default: ''
    }
  },
  /* translators: block description */
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Add several checkbox items.', 'affiliate-wp'),
  keywords: ['affiliatewp', /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('option', 'affiliatewp')],
  supports: {
    reusable: false,
    html: false
  },
  edit: editMultiField('checkbox'),
  save: function save() {
    return null;
  }
};


/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/checkbox/edit.js":
/*!**********************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/checkbox/edit.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__);

/**
 * Affiliate Registration Form Checkbox Edit Component.
 *
 * @since 2.8
 */

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */



function AffiliateWPFieldCheckbox(_ref) {
  var attributes = _ref.attributes,
    setAttributes = _ref.setAttributes,
    isSelected = _ref.isSelected,
    resetFocus = _ref.resetFocus,
    name = _ref.name,
    context = _ref.context,
    clientId = _ref.clientId;
  var required = attributes.required,
    label = attributes.label;
  var blockProps = Object(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["useBlockProps"])();
  var fieldClassNames = classnames__WEBPACK_IMPORTED_MODULE_1___default()('affwp-field', 'affwp-field-checkbox');
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["_x"])('Field settings', 'Checkbox field', 'affiliate-wp'),
    initialOpen: true
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["ToggleControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["_x"])('Required', 'Checkbox field', 'affiliate-wp'),
    className: "affwp-field-label__required",
    checked: required,
    onChange: function onChange(required) {
      return setAttributes({
        required: required
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["_x"])('Field Label', 'Checkbox field', 'affiliate-wp'),
    value: label,
    onChange: function onChange(label) {
      return setAttributes({
        label: label
      });
    }
  }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", blockProps, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("input", {
    className: fieldClassNames,
    type: "checkbox"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["RichText"], {
    identifier: 'label',
    tagName: "label",
    value: label,
    onChange: function onChange(label) {
      if (resetFocus) {
        resetFocus();
      }
      setAttributes({
        label: label
      });
    },
    placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["_x"])('Add label ...', 'Checkbox field', 'affiliate-wp')
  })));
}
/* harmony default export */ __webpack_exports__["default"] = (AffiliateWPFieldCheckbox);

/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/checkbox/index.js":
/*!***********************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/checkbox/index.js ***!
  \***********************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit */ "./assets/js/editor/blocks/registration/fields/checkbox/edit.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);

/**
 * Affiliate registration checkbox field Block.
 *
 * @since 2.8
 */

/**
 * Internal dependencies
 */


/**
 * WordPress dependencies
 */


var icon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Icon"], {
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24",
    height: 24,
    width: 24
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("defs", null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("title", null, "check-2"), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M6,13.223,8.45,16.7a1.049,1.049,0,0,0,1.707.051L18,6.828",
    fill: "none",
    stroke: "#000000",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: "2px"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("rect", {
    x: 0.75,
    y: 0.749,
    width: 22.5,
    height: 22.5,
    rx: 1.5,
    ry: 1.5,
    fill: "none",
    stroke: "#000000",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: "2px"
  }))
});
var name = 'affiliatewp/field-checkbox';
var settings = {
  /* translators: block name */
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Checkbox', 'affiliate-wp'),
  category: 'affiliatewp',
  parent: ['affiliatewp/registration'],
  icon: icon,
  attributes: {
    label: {
      type: 'string',
      default: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Option', 'affiliate-wp')
    },
    required: {
      type: 'boolean',
      default: false
    }
  },
  /* translators: block description */
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Add a single checkbox.', 'affiliate-wp'),
  keywords: ['affiliatewp', /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('checkbox', 'affiliate-wp')],
  supports: {
    html: false,
    lightBlockWrapper: true
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_1__["default"],
  save: function save() {
    return null;
  }
};


/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/email/edit.js":
/*!*******************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/email/edit.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_email_edit__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../components/email-edit */ "./assets/js/editor/blocks/registration/components/email-edit.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__);

/**
 * Affiliate Registration Form Email field Edit Component.
 *
 * @since 2.8
 */

/**
 * External dependencies
 */


/**
 * Internal dependencies
 */


/**
 * WordPress dependencies
 */




function AffiliateWPFieldEmail(_ref) {
  var attributes = _ref.attributes,
    setAttributes = _ref.setAttributes,
    isSelected = _ref.isSelected,
    name = _ref.name,
    context = _ref.context,
    clientId = _ref.clientId;
  var required = attributes.required,
    label = attributes.label,
    classNames = attributes.classNames,
    placeholder = attributes.placeholder,
    type = attributes.type;
  var fieldClassNames = classnames__WEBPACK_IMPORTED_MODULE_1___default()('affwp-field', 'affwp-field-email');
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_components_email_edit__WEBPACK_IMPORTED_MODULE_2__["default"], {
    attributes: attributes,
    setAttributes: setAttributes,
    isSelected: isSelected,
    name: name,
    context: context,
    clientId: clientId
  });
}
/* harmony default export */ __webpack_exports__["default"] = (AffiliateWPFieldEmail);

/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/email/index.js":
/*!********************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/email/index.js ***!
  \********************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit */ "./assets/js/editor/blocks/registration/fields/email/edit.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);

/**
 * Affiliate registration email field Block.
 *
 * @since 2.8
 */

/**
 * Internal dependencies
 */


/**
 * WordPress dependencies
 */


var icon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Icon"], {
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    className: "h-6 w-6",
    fill: "none",
    viewBox: "0 0 24 24",
    stroke: "currentColor"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: 2,
    d: "M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207",
    fill: "none"
  }))
});
var name = 'affiliatewp/field-email';
var settings = {
  /* translators: block name */
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Email', 'affiliate-wp'),
  category: 'affiliatewp',
  parent: ['affiliatewp/registration'],
  icon: icon,
  attributes: {
    label: {
      type: 'string',
      default: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Email Address', 'affiliate-wp')
    },
    required: {
      type: 'boolean',
      default: false
    },
    placeholder: {
      type: 'string'
    },
    type: {
      type: 'string'
    }
  },
  /* translators: block description */
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('A field for collecting a validated email address.', 'affiliate-wp'),
  keywords: ['affiliatewp', /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('e-mail', 'affiliate-wp'), /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('mail', 'affiliate-wp')],
  supports: {
    html: false,
    lightBlockWrapper: true
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_1__["default"],
  save: function save() {
    return null;
  }
};


/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/name/edit.js":
/*!******************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/name/edit.js ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _components_text_edit__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../components/text-edit */ "./assets/js/editor/blocks/registration/components/text-edit.js");

/**
 * Affiliate Registration Form name field Edit Component.
 *
 * @since 2.8
 */

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */

function AffiliateWPFieldText(_ref) {
  var attributes = _ref.attributes,
    setAttributes = _ref.setAttributes,
    isSelected = _ref.isSelected,
    name = _ref.name,
    context = _ref.context,
    clientId = _ref.clientId;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_components_text_edit__WEBPACK_IMPORTED_MODULE_3__["default"], {
    attributes: attributes,
    setAttributes: setAttributes,
    isSelected: isSelected,
    name: name,
    context: context
  });
}
/* harmony default export */ __webpack_exports__["default"] = (AffiliateWPFieldText);

/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/name/index.js":
/*!*******************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/name/index.js ***!
  \*******************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit */ "./assets/js/editor/blocks/registration/fields/name/edit.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);

/**
 * Affiliate registration name field Block.
 *
 * @since 2.8
 */

/**
 * Internal dependencies
 */


/**
 * WordPress dependencies
 */


var icon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Icon"], {
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    fill: "none",
    viewBox: "0 0 24 24",
    stroke: "currentColor"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: 2,
    d: "M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z",
    fill: "none"
  }))
});
var name = 'affiliatewp/field-name';
var settings = {
  /* translators: block name */
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Name', 'affiliate-wp'),
  category: 'affiliatewp',
  parent: ['affiliatewp/registration'],
  icon: icon,
  attributes: {
    label: {
      type: 'string',
      default: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Your Name', 'affiliate-wp')
    },
    placeholder: {
      type: 'string'
    },
    required: {
      type: 'boolean',
      default: false
    },
    type: {
      type: 'string'
    }
  },
  /* translators: block description */
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('The affiliate\'s name.', 'affiliate-wp'),
  keywords: ['affiliatewp', /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('name', 'affiliatewp'), /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('text', 'affiliatewp')],
  supports: {
    html: false,
    lightBlockWrapper: true
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_1__["default"],
  save: function save() {
    return null;
  }
};


/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/password/edit.js":
/*!**********************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/password/edit.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_field_label__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../components/field-label */ "./assets/js/editor/blocks/registration/components/field-label.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__);

/**
 * Affiliate Registration Form password field Edit Component.
 *
 * @since 2.8
 */

/**
 * External dependencies
 */


/**
 * Internal dependencies
 */


/**
 * WordPress dependencies
 */



function AffiliateWPFieldPassword(_ref) {
  var attributes = _ref.attributes,
    setAttributes = _ref.setAttributes,
    isSelected = _ref.isSelected,
    name = _ref.name,
    context = _ref.context;
  var required = attributes.required,
    label = attributes.label,
    labelConfirm = attributes.labelConfirm,
    placeholder = attributes.placeholder,
    placeholderConfirm = attributes.placeholderConfirm;
  var blockProps = Object(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["useBlockProps"])();
  var fieldClassNames = classnames__WEBPACK_IMPORTED_MODULE_1___default()('affwp-field', 'affwp-field-password');
  var showPlaceholders = context['affiliatewp/placeholders'];
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["Notice"], {
    className: "affwp-block-inspector-notice",
    isDismissible: false,
    status: "warning"
  }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('The Password fields will only show on the Affiliate Registration form to logged out users.', 'affiliate-wp')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Field settings', 'affiliate-wp'),
    initialOpen: true
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Password Field Label', 'affiliate-wp'),
    value: label,
    onChange: function onChange(label) {
      return setAttributes({
        label: label
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Password Field Placeholder', 'affiliate-wp'),
    value: placeholder,
    onChange: function onChange(placeholder) {
      return setAttributes({
        placeholder: placeholder
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Password Confirm Field Label', 'affiliate-wp'),
    value: labelConfirm,
    onChange: function onChange(labelConfirm) {
      return setAttributes({
        labelConfirm: labelConfirm
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Password Confirm Field Placeholder', 'affiliate-wp'),
    value: placeholderConfirm,
    onChange: function onChange(placeholderConfirm) {
      return setAttributes({
        placeholderConfirm: placeholderConfirm
      });
    }
  }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", blockProps, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
    style: {
      marginBottom: 28
    }
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_components_field_label__WEBPACK_IMPORTED_MODULE_2__["default"], {
    identifier: "label",
    required: required,
    requiredAttribute: 'required',
    label: label,
    labelAttribute: 'label',
    setAttributes: setAttributes,
    isSelected: isSelected,
    name: name,
    context: context
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["RichText"], {
    identifier: "placeholder",
    placeholder: showPlaceholders ? Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Add placeholder text…') : '',
    value: placeholder,
    onChange: function onChange(placeholder) {
      return setAttributes({
        placeholder: placeholder
      });
    },
    allowedFormats: [],
    type: 'text',
    className: fieldClassNames
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_components_field_label__WEBPACK_IMPORTED_MODULE_2__["default"], {
    identifier: "labelConfirm",
    required: required,
    requiredAttribute: 'required',
    label: labelConfirm,
    labelAttribute: 'labelConfirm',
    setAttributes: setAttributes,
    isSelected: isSelected,
    name: name,
    context: context
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["RichText"], {
    identifier: "placeholderConfirm",
    placeholder: showPlaceholders ? Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Add placeholder text…') : '',
    value: placeholderConfirm,
    onChange: function onChange(placeholderConfirm) {
      return setAttributes({
        placeholderConfirm: placeholderConfirm
      });
    },
    allowedFormats: [],
    type: 'text',
    className: fieldClassNames
  }))));
}
/* harmony default export */ __webpack_exports__["default"] = (AffiliateWPFieldPassword);

/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/password/index.js":
/*!***********************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/password/index.js ***!
  \***********************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit */ "./assets/js/editor/blocks/registration/fields/password/edit.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);

/**
 * Affiliate registration password field Block.
 *
 * @since 2.8
 */

/**
 * Internal dependencies
 */


/**
 * WordPress dependencies
 */


var icon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Icon"], {
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    fill: "none",
    viewBox: "0 0 24 24",
    stroke: "currentColor"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: 2,
    d: "M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z",
    fill: "none"
  }))
});
var name = 'affiliatewp/field-password';
var settings = {
  /* translators: block name */
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Password', 'affiliate-wp'),
  category: 'affiliatewp',
  parent: ['affiliatewp/registration'],
  icon: icon,
  attributes: {
    label: {
      type: 'string',
      default: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Password', 'affiliate-wp')
    },
    labelConfirm: {
      type: 'string',
      default: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Confirm Password', 'affiliate-wp')
    },
    placeholder: {
      type: 'string'
    },
    placeholderConfirm: {
      type: 'string'
    }
  },
  /* translators: block description */
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('A field for collecting the affiliate\'s desired password.', 'affiliate-wp'),
  keywords: ['affiliatewp', /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('password', 'affiliate-wp')],
  supports: {
    html: false,
    multiple: false,
    lightBlockWrapper: true
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_1__["default"],
  save: function save() {
    return null;
  }
};


/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/payment-email/edit.js":
/*!***************************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/payment-email/edit.js ***!
  \***************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_email_edit__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../components/email-edit */ "./assets/js/editor/blocks/registration/components/email-edit.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__);

/**
 * Affiliate Registration Form payment Email field Edit Component.
 *
 * @since 2.8
 */

/**
 * External dependencies
 */


/**
 * Internal dependencies
 */


/**
 * WordPress dependencies
 */




function AffiliateWPFieldEmail(_ref) {
  var attributes = _ref.attributes,
    setAttributes = _ref.setAttributes,
    isSelected = _ref.isSelected,
    name = _ref.name,
    context = _ref.context,
    clientId = _ref.clientId;
  var required = attributes.required,
    label = attributes.label,
    classNames = attributes.classNames,
    placeholder = attributes.placeholder,
    type = attributes.type;
  var fieldClassNames = classnames__WEBPACK_IMPORTED_MODULE_1___default()('affwp-field', 'affwp-field-email');
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_components_email_edit__WEBPACK_IMPORTED_MODULE_2__["default"], {
    attributes: attributes,
    setAttributes: setAttributes,
    isSelected: isSelected,
    name: name,
    context: context,
    clientId: clientId
  });
}
/* harmony default export */ __webpack_exports__["default"] = (AffiliateWPFieldEmail);

/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/payment-email/index.js":
/*!****************************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/payment-email/index.js ***!
  \****************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit */ "./assets/js/editor/blocks/registration/fields/payment-email/edit.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);

/**
 * Affiliate registration payment email field Block.
 *
 * @since 2.8
 */

/**
 * Internal dependencies
 */


/**
 * WordPress dependencies
 */


var icon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Icon"], {
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    className: "h-6 w-6",
    fill: "none",
    viewBox: "0 0 24 24",
    stroke: "currentColor"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: 2,
    d: "M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207",
    fill: "none"
  }))
});
var name = 'affiliatewp/field-payment-email';
var settings = {
  /* translators: block name */
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Payment Email', 'affiliate-wp'),
  category: 'affiliatewp',
  parent: ['affiliatewp/registration'],
  icon: icon,
  attributes: {
    label: {
      type: 'string',
      default: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Email Address', 'affiliate-wp')
    },
    required: {
      type: 'boolean',
      default: false
    },
    placeholder: {
      type: 'string'
    },
    type: {
      type: 'string'
    }
  },
  /* translators: block description */
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('A field for collecting a valid payment email address.', 'affiliate-wp'),
  keywords: ['affiliatewp', /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('e-mail', 'affiliate-wp'), /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('mail', 'affiliate-wp'), /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('payment', 'affiliate-wp')],
  supports: {
    html: false,
    lightBlockWrapper: true
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_1__["default"],
  save: function save() {
    return null;
  }
};


/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/phone/edit.js":
/*!*******************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/phone/edit.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_field__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../components/field */ "./assets/js/editor/blocks/registration/components/field.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__);

/**
 * Affiliate Registration Form phone field Edit Component.
 *
 * @since 2.8
 */

/**
 * External dependencies
 */


/**
 * Internal dependencies
 */


/**
 * WordPress dependencies
 */



function AffiliateWPFieldPhone(_ref) {
  var attributes = _ref.attributes,
    setAttributes = _ref.setAttributes,
    isSelected = _ref.isSelected,
    name = _ref.name,
    context = _ref.context,
    clientId = _ref.clientId;
  var required = attributes.required,
    label = attributes.label,
    classNames = attributes.classNames,
    placeholder = attributes.placeholder;
  var fieldClassNames = classnames__WEBPACK_IMPORTED_MODULE_1___default()('affwp-field', 'affwp-field-phone');
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Field settings', 'affiliate-wp'),
    initialOpen: true
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["ToggleControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Required', 'affiliate-wp'),
    className: "affwp-field-label__required",
    checked: required,
    onChange: function onChange(required) {
      return setAttributes({
        required: required
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Field Label', 'affiliate-wp'),
    value: label,
    onChange: function onChange(label) {
      return setAttributes({
        label: label
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Field Placeholder', 'affiliate-wp'),
    value: placeholder,
    onChange: function onChange(placeholder) {
      return setAttributes({
        placeholder: placeholder
      });
    }
  }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_components_field__WEBPACK_IMPORTED_MODULE_2__["default"], {
    label: label,
    type: "tel",
    required: required,
    setAttributes: setAttributes,
    isSelected: isSelected,
    name: name,
    classNames: classNames,
    fieldClassNames: fieldClassNames,
    placeholder: placeholder,
    context: context
  }));
}
/* harmony default export */ __webpack_exports__["default"] = (AffiliateWPFieldPhone);

/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/phone/index.js":
/*!********************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/phone/index.js ***!
  \********************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit */ "./assets/js/editor/blocks/registration/fields/phone/edit.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);

/**
 * Affiliate registration phone field Block.
 *
 * @since 2.8
 */

/**
 * Internal dependencies
 */


/**
 * WordPress dependencies
 */


var icon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Icon"], {
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    fill: "none",
    viewBox: "0 0 24 24",
    stroke: "currentColor"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: 2,
    d: "M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z",
    fill: "none"
  }))
});
var name = 'affiliatewp/field-phone';
var settings = {
  /* translators: block name */
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Phone', 'affiliate-wp'),
  category: 'affiliatewp',
  parent: ['affiliatewp/registration'],
  icon: icon,
  attributes: {
    label: {
      type: 'string',
      default: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Phone Number', 'affiliate-wp')
    },
    required: {
      type: 'boolean',
      default: false
    },
    placeholder: {
      type: 'string'
    }
  },
  /* translators: block description */
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('A field for collecting a phone number.', 'affiliate-wp'),
  keywords: ['affiliatewp', /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('phone', 'affiliatewp')],
  supports: {
    html: false,
    lightBlockWrapper: true
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_1__["default"],
  save: function save() {
    return null;
  }
};


/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/radio/index.js":
/*!********************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/radio/index.js ***!
  \********************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _components_field_multiple__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../components/field-multiple */ "./assets/js/editor/blocks/registration/components/field-multiple.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__);

/**
 * Affiliate registration Radio field Block.
 *
 * @since 2.10.0
 */



/**
 * WordPress dependencies
 */



var icon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Icon"], {
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    width: "25",
    height: "25",
    viewBox: "0 0 25 25",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M12.25 23.5C18.4632 23.5 23.5 18.4632 23.5 12.25C23.5 6.0368 18.4632 1 12.25 1C6.0368 1 1 6.0368 1 12.25C1 18.4632 6.0368 23.5 12.25 23.5Z",
    stroke: "black",
    strokeWidth: "2.0",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    fill: "none"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M12.25 17C14.8734 17 17 14.8734 17 12.25C17 9.62665 14.8734 7.5 12.25 7.5C9.62665 7.5 7.5 9.62665 7.5 12.25C7.5 14.8734 9.62665 17 12.25 17Z",
    stroke: "black",
    strokeWidth: "2.0",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    fill: "none"
  }))
});
var name = 'affiliatewp/field-radio';
var getFieldLabel = function getFieldLabel(_ref) {
  var attributes = _ref.attributes,
    blockName = _ref.name;
  return null === attributes.label ? Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["getBlockType"])(blockName).title : attributes.label;
};
var editMultiField = function editMultiField(type) {
  return function (props) {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_components_field_multiple__WEBPACK_IMPORTED_MODULE_1__["default"], {
      label: getFieldLabel(props),
      required: props.attributes.required,
      options: props.attributes.options,
      setAttributes: props.setAttributes,
      type: type,
      isSelected: props.isSelected,
      id: props.attributes.id
    });
  };
};
var settings = {
  apiVersion: 2,
  /* translators: block name */
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Single Choice (Radio)', 'affiliate-wp'),
  category: 'affiliatewp',
  parent: ['affiliatewp/registration'],
  icon: icon,
  attributes: {
    label: {
      type: 'string',
      default: 'Choose one option'
    },
    required: {
      type: 'boolean',
      default: false
    },
    options: {
      type: 'array',
      default: []
    },
    defaultValue: {
      type: 'string',
      default: ''
    },
    placeholder: {
      type: 'string',
      default: ''
    },
    id: {
      type: 'string',
      default: ''
    }
  },
  /* translators: block description */
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Add one or more radio buttons.', 'affiliate-wp'),
  keywords: ['affiliatewp', /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('radio', 'affiliatewp'), /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('option', 'affiliatewp')],
  supports: {
    reusable: false,
    html: false
  },
  edit: editMultiField('radio'),
  save: function save() {
    return null;
  }
};


/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/register-button/edit.js":
/*!*****************************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/register-button/edit.js ***!
  \*****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__);

/**
 * Affiliate Registration Form Email register button Edit Component.
 *
 * @since 2.8
 */

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */



function AffiliateWPFieldSubmitButton(_ref) {
  var attributes = _ref.attributes,
    setAttributes = _ref.setAttributes,
    isSelected = _ref.isSelected,
    name = _ref.name,
    clientId = _ref.clientId;
  var text = attributes.text;
  var blockProps = Object(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["useBlockProps"])();
  var classes = classnames__WEBPACK_IMPORTED_MODULE_1___default()('affwp-button-register');
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Button Settings', 'affiliate-wp'),
    initialOpen: true
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Button Text', 'affiliate-wp'),
    value: text,
    onChange: function onChange(text) {
      return setAttributes({
        text: text
      });
    }
  }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", blockProps, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__["RichText"], {
    identifier: "text",
    placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Add button text…', 'affiliate-wp'),
    value: text,
    onChange: function onChange(text) {
      return setAttributes({
        text: text
      });
    },
    withoutInteractiveFormatting: true,
    allowedFormats: [],
    className: classes
  })));
}
/* harmony default export */ __webpack_exports__["default"] = (AffiliateWPFieldSubmitButton);

/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/register-button/index.js":
/*!******************************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/register-button/index.js ***!
  \******************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit */ "./assets/js/editor/blocks/registration/fields/register-button/edit.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);

/**
 * Affiliate registration register button Block.
 *
 * @since 2.8
 */

/**
 * Internal dependencies
 */


/**
 * WordPress dependencies
 */


var icon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Icon"], {
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    fill: "none",
    viewBox: "0 0 24 24",
    stroke: "currentColor"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: 2,
    d: "M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122",
    fill: "none"
  }))
});

/**
 * Block constants
 */

var name = 'affiliatewp/field-register-button';
var settings = {
  category: 'affiliatewp',
  icon: icon,
  attributes: {
    placeholder: {
      type: 'string'
    },
    text: {
      type: 'string',
      default: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Register', 'affiliate-wp')
    }
  },
  /* translators: block name */
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Register Button', 'affiliate-wp'),
  /* translators: block description */
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('A button for submitting the affiliate registration.', 'affiliate-wp'),
  keywords: ['affiliatewp', /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('submit', 'affiliate-wp'), /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('button', 'affiliate-wp'), /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('register', 'affiliate-wp')],
  parent: ['affiliatewp/registration'],
  supports: {
    reusable: false,
    html: false,
    multiple: false,
    lightBlockWrapper: true
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_1__["default"],
  save: function save() {
    return null;
  }
};


/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/select/index.js":
/*!*********************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/select/index.js ***!
  \*********************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _components_field_multiple__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../components/field-multiple */ "./assets/js/editor/blocks/registration/components/field-multiple.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__);

/**
 * Affiliate registration Select field Block.
 *
 * @since 2.10.0
 */



/**
 * WordPress dependencies
 */



var icon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Icon"], {
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    width: "24",
    height: "24",
    viewBox: "0 0 24 24",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M2.75 23.25L21.25 23.25C22.3546 23.25 23.25 22.3546 23.25 21.25L23.25 2.75C23.25 1.64543 22.3546 0.75 21.25 0.75L2.75 0.75C1.64543 0.75 0.75 1.64543 0.75 2.75L0.75 21.25C0.75 22.3546 1.64543 23.25 2.75 23.25Z",
    stroke: "black",
    strokeWidth: "2",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    fill: "none"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M17.25 9L12.592 14.989C12.5219 15.0791 12.4321 15.1521 12.3295 15.2023C12.2269 15.2524 12.1142 15.2785 12 15.2785C11.8858 15.2785 11.7731 15.2524 11.6705 15.2023C11.5679 15.1521 11.4781 15.0791 11.408 14.989L6.75 9",
    stroke: "black",
    strokeWidth: "2",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    fill: "none"
  }))
});
var name = 'affiliatewp/field-select';
var getFieldLabel = function getFieldLabel(_ref) {
  var attributes = _ref.attributes,
    blockName = _ref.name;
  return null === attributes.label ? Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_4__["getBlockType"])(blockName).title : attributes.label;
};
var editMultiField = function editMultiField(type) {
  return function (props) {
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_components_field_multiple__WEBPACK_IMPORTED_MODULE_1__["default"], {
      label: getFieldLabel(props),
      required: props.attributes.required,
      options: props.attributes.options,
      setAttributes: props.setAttributes,
      type: type,
      isSelected: props.isSelected,
      id: props.attributes.id
    });
  };
};
var settings = {
  apiVersion: 2,
  /* translators: block name */
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Dropdown Field', 'affiliate-wp'),
  category: 'affiliatewp',
  parent: ['affiliatewp/registration'],
  icon: icon,
  attributes: {
    label: {
      type: 'string',
      default: 'Select one'
    },
    required: {
      type: 'boolean',
      default: false
    },
    options: {
      type: 'array',
      default: []
    },
    defaultValue: {
      type: 'string',
      default: ''
    },
    placeholder: {
      type: 'string',
      default: ''
    },
    id: {
      type: 'string',
      default: ''
    }
  },
  /* translators: block description */
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Add a select box with several items.', 'affiliate-wp'),
  keywords: ['affiliatewp', /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('select', 'affiliatewp')],
  supports: {
    reusable: false,
    html: false
  },
  edit: editMultiField('select'),
  save: function save() {
    return null;
  }
};


/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/terms-of-use/edit.js":
/*!**************************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/terms-of-use/edit.js ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "./node_modules/@babel/runtime/helpers/slicedToArray.js");
/* harmony import */ var _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__);


/**
 * Affiliate Registration Form Terms of Use Edit Component.
 *
 * @since 2.10.0
 */

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */





function AffiliateWPFieldTermsOfUse(_ref) {
  var attributes = _ref.attributes,
    setAttributes = _ref.setAttributes,
    isSelected = _ref.isSelected,
    resetFocus = _ref.resetFocus,
    name = _ref.name,
    context = _ref.context,
    clientId = _ref.clientId;
  var required = attributes.required,
    label = attributes.label,
    link = attributes.link,
    id = attributes.id,
    style = attributes.style;
  var _useState = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["useState"])(false),
    _useState2 = _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0___default()(_useState, 2),
    placeholder = _useState2[0],
    setPlaceholder = _useState2[1];
  var LinkControl = _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["LinkControl"] ? _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["LinkControl"] : _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["__experimentalLinkControl"];
  var pageContent = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_6__["useSelect"])(function (select) {
    var _pageContent$content;
    if (!id) {
      return;
    }
    var pageContent = select('core').getEntityRecord('postType', 'page', id);
    return pageContent === null || pageContent === void 0 ? void 0 : (_pageContent$content = pageContent.content) === null || _pageContent$content === void 0 ? void 0 : _pageContent$content.raw;
  });
  var blockProps = Object(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["useBlockProps"])();
  var fieldClassNames = classnames__WEBPACK_IMPORTED_MODULE_2___default()('affwp-field', 'affwp-field-terms-of-use');
  var icon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["Icon"], {
    icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("svg", {
      xmlns: "http://www.w3.org/2000/svg",
      viewBox: "0 0 24 24",
      height: 24,
      width: 24
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("g", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("path", {
      d: "M17.25.75H3.75a3,3,0,0,0-3,3v18a1.5,1.5,0,0,0,1.5,1.5H3.68",
      fill: "none",
      stroke: "#000000",
      strokeLinecap: "round",
      strokeLinejoin: "round",
      strokeWidth: "2px"
    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("line", {
      x1: 3.75,
      y1: 5.5,
      x2: 11,
      y2: 5.5,
      fill: "none",
      stroke: "#000000",
      strokeLinecap: "round",
      strokeLinejoin: "round",
      strokeWidth: "2px"
    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("line", {
      x1: 3.75,
      y1: 9.5,
      x2: 8.61,
      y2: 9.5,
      fill: "none",
      stroke: "#000000",
      strokeLinecap: "round",
      strokeLinejoin: "round",
      strokeWidth: "2px"
    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("path", {
      d: "M15,9.75V3A2.25,2.25,0,0,1,17.25.75h0A2.25,2.25,0,0,1,19.5,3V5.5H15",
      fill: "none",
      stroke: "#000000",
      strokeLinecap: "round",
      strokeLinejoin: "round",
      strokeWidth: "2px"
    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("g", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("line", {
      x1: 21.75,
      y1: 19.61,
      x2: 16.96,
      y2: 20.57,
      fill: "none",
      stroke: "#000000",
      strokeLinecap: "round",
      strokeLinejoin: "round",
      strokeWidth: "2px"
    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("path", {
      d: "M17.44,15.14l-2.26.95a1.41,1.41,0,0,1-1.12,0A1.52,1.52,0,0,1,14,13.35l2.26-1.13a2,2,0,0,1,.9-.22,1.8,1.8,0,0,1,.69.13L22.47,14",
      fill: "none",
      stroke: "#000000",
      strokeLinecap: "round",
      strokeLinejoin: "round",
      strokeWidth: "2px"
    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("path", {
      d: "M8.2,20.61H9.79l3.05,2.32A.82.82,0,0,0,14,23l4.26-3.52a.83.83,0,0,0,.13-1.16L16,15.73",
      fill: "none",
      stroke: "#000000",
      strokeLinecap: "round",
      strokeLinejoin: "round",
      strokeWidth: "2px"
    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("path", {
      d: "M13.74,13.51l-.25-.21A1.83,1.83,0,0,0,12.43,13a1.93,1.93,0,0,0-.67.12L8.19,14.6",
      fill: "none",
      stroke: "#000000",
      strokeLinecap: "round",
      strokeLinejoin: "round",
      strokeWidth: "2px"
    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("path", {
      d: "M6.75,21.36h.3a1.14,1.14,0,0,0,1.2-1.08V14.93a1.14,1.14,0,0,0-1.2-1.07h-.3",
      fill: "none",
      stroke: "#000000",
      strokeLinecap: "round",
      strokeLinejoin: "round",
      strokeWidth: "2px"
    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("path", {
      d: "M23.25,21.36H23a1.14,1.14,0,0,1-1.2-1.08V14.93A1.14,1.14,0,0,1,23,13.86h.3",
      fill: "none",
      stroke: "#000000",
      strokeLinecap: "round",
      strokeLinejoin: "round",
      strokeWidth: "2px"
    }))))
  });
  Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["useEffect"])(function () {
    if (link) {
      return;
    }
    if (1 === style) {
      setPlaceholder(true);
    }
    return function () {
      setPlaceholder(false);
    };
  }, [link]);
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", blockProps, false === placeholder && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Field settings', 'affiliate-wp'),
    initialOpen: true,
    className: "panel-terms-of-use"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["ToggleControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Required', 'affiliate-wp'),
    className: "affwp-field-label__required",
    checked: required,
    onChange: function onChange(required) {
      return setAttributes({
        required: required
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["RadioControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Display Style', 'affiliate-wp'),
    selected: style,
    options: [{
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Default', 'affiliate-wp'),
      value: 1
    }, {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Show above checkbox', 'affiliate-wp'),
      value: 2
    }],
    onChange: function onChange(value) {
      return setAttributes({
        style: parseInt(value)
      });
    }
  }), 2 === style && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["BaseControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Page Content to Display', 'affiliate-wp'),
    __nextHasNoMarginBottom: true
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(LinkControl, {
    searchInputPlaceholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Terms of Use Page', 'affiliate-wp'),
    value: {
      url: attributes.link
    },
    onChange: function onChange(value) {
      setAttributes({
        link: value.url,
        id: 'URL' !== value.type ? value.id : undefined
      });
    },
    onRemove: function onRemove() {
      setAttributes({
        link: undefined,
        id: undefined
      });
    },
    settings: [],
    suggestionsQuery: {
      type: "post",
      subtype: 'page'
    }
  })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Field Label', 'affiliate-wp'),
    value: label,
    onChange: function onChange(label) {
      return setAttributes({
        label: label
      });
    }
  }))), true === placeholder ? /* The Placeholder component is only shown when the block is first inserted, and there's no Terms of Use page selected in the settings */
  Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["Placeholder"], {
    icon: icon,
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Affiliate Terms of Use', 'affiliate-wp')
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("p", null, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Select your Affiliate Terms of Use page below.', 'affiliate-wp'), " ", Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__["ExternalLink"], {
    href: affwp_blocks.terms_of_use_generator
  }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Create one using a template', 'affiliate-wp'))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(LinkControl, {
    searchInputPlaceholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Select a Terms of Use Page', 'affiliate-wp'),
    value: {
      url: attributes.url
    },
    onChange: function onChange(value) {
      setAttributes({
        link: value.url,
        id: 'URL' !== value.type ? value.id : undefined,
        // Set the label for the first time.
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["sprintf"])(
        // translators: %1$s: open link tag, %2$s close link tag
        Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Agree to our %1$sTerms of Use and Privacy Policy%2$s', 'affiliate-wp'), "<a href=\"".concat(value.url, "\" target=\"_blank\">"), '</a>')
      });
    },
    settings: [],
    suggestionsQuery: {
      type: "post",
      subtype: 'page'
    }
  })) : Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, 1 === style && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("input", {
    className: fieldClassNames,
    type: "checkbox"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["RichText"], {
    identifier: 'label',
    tagName: "label",
    value: label,
    onChange: function onChange(label) {
      if (resetFocus) {
        resetFocus();
      }
      setAttributes({
        label: label
      });
    },
    placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Add label ...', 'affiliate-wp')
  })), 2 === style && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, !id && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("p", {
    className: "affwp-error-notice"
  }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('No Terms of Use page selected.', 'affiliate-wp')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
    className: "affwp-field-terms-of-use-content",
    dangerouslySetInnerHTML: {
      __html: pageContent
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("input", {
    className: fieldClassNames,
    type: "checkbox"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__["RichText"], {
    identifier: 'label',
    tagName: "label",
    value: label,
    onChange: function onChange(label) {
      if (resetFocus) {
        resetFocus();
      }
      setAttributes({
        label: label
      });
    },
    placeholder: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Add label ...', 'affiliate-wp')
  }))));
}
/* harmony default export */ __webpack_exports__["default"] = (AffiliateWPFieldTermsOfUse);

/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/terms-of-use/index.js":
/*!***************************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/terms-of-use/index.js ***!
  \***************************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit */ "./assets/js/editor/blocks/registration/fields/terms-of-use/edit.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
var _affwp_blocks, _affwp_blocks2, _affwp_blocks3;

/**
 * Affiliate registration Terms of Use field Block.
 *
 * @since 2.10.0
 */

/**
 * Internal dependencies
 */


/**
 * WordPress dependencies
 */


var icon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Icon"], {
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24",
    height: 24,
    width: 24
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("g", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M17.25.75H3.75a3,3,0,0,0-3,3v18a1.5,1.5,0,0,0,1.5,1.5H3.68",
    fill: "none",
    stroke: "#000000",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: "2px"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("line", {
    x1: 3.75,
    y1: 5.5,
    x2: 11,
    y2: 5.5,
    fill: "none",
    stroke: "#000000",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: "2px"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("line", {
    x1: 3.75,
    y1: 9.5,
    x2: 8.61,
    y2: 9.5,
    fill: "none",
    stroke: "#000000",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: "2px"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M15,9.75V3A2.25,2.25,0,0,1,17.25.75h0A2.25,2.25,0,0,1,19.5,3V5.5H15",
    fill: "none",
    stroke: "#000000",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: "2px"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("g", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("line", {
    x1: 21.75,
    y1: 19.61,
    x2: 16.96,
    y2: 20.57,
    fill: "none",
    stroke: "#000000",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: "2px"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M17.44,15.14l-2.26.95a1.41,1.41,0,0,1-1.12,0A1.52,1.52,0,0,1,14,13.35l2.26-1.13a2,2,0,0,1,.9-.22,1.8,1.8,0,0,1,.69.13L22.47,14",
    fill: "none",
    stroke: "#000000",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: "2px"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M8.2,20.61H9.79l3.05,2.32A.82.82,0,0,0,14,23l4.26-3.52a.83.83,0,0,0,.13-1.16L16,15.73",
    fill: "none",
    stroke: "#000000",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: "2px"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M13.74,13.51l-.25-.21A1.83,1.83,0,0,0,12.43,13a1.93,1.93,0,0,0-.67.12L8.19,14.6",
    fill: "none",
    stroke: "#000000",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: "2px"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M6.75,21.36h.3a1.14,1.14,0,0,0,1.2-1.08V14.93a1.14,1.14,0,0,0-1.2-1.07h-.3",
    fill: "none",
    stroke: "#000000",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: "2px"
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    d: "M23.25,21.36H23a1.14,1.14,0,0,1-1.2-1.08V14.93A1.14,1.14,0,0,1,23,13.86h.3",
    fill: "none",
    stroke: "#000000",
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: "2px"
  }))))
});
var name = 'affiliatewp/field-terms-of-use';
var termsOfUse = (_affwp_blocks = affwp_blocks) === null || _affwp_blocks === void 0 ? void 0 : _affwp_blocks.terms_of_use;
var termsOfUseLink = (_affwp_blocks2 = affwp_blocks) === null || _affwp_blocks2 === void 0 ? void 0 : _affwp_blocks2.terms_of_use_link;
var termsOfUseLabel = (_affwp_blocks3 = affwp_blocks) === null || _affwp_blocks3 === void 0 ? void 0 : _affwp_blocks3.terms_of_use_label;
var settings = {
  /* translators: block name */
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Affiliate Terms of Use', 'affiliate-wp'),
  category: 'affiliatewp',
  parent: ['affiliatewp/registration'],
  icon: icon,
  attributes: {
    label: {
      type: 'string',
      default: termsOfUseLabel
    },
    required: {
      type: 'boolean',
      default: true
    },
    link: {
      type: 'string',
      default: termsOfUseLink
    },
    id: {
      type: 'number',
      default: termsOfUse
    },
    style: {
      type: 'number',
      default: 1
    }
  },
  /* translators: block description */
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Display an Affiliate Terms of Use checkbox which affiliates must agree to.', 'affiliate-wp'),
  keywords: ['affiliatewp', /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('checkbox', 'affiliate-wp'), /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('terms of use', 'affiliate-wp'), /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('affiliate terms', 'affiliate-wp')],
  supports: {
    html: false,
    lightBlockWrapper: true
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_1__["default"],
  save: function save() {
    return null;
  }
};


/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/text/edit.js":
/*!******************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/text/edit.js ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _components_text_edit__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../components/text-edit */ "./assets/js/editor/blocks/registration/components/text-edit.js");

/**
 * Affiliate Registration Form text field Edit Component.
 *
 * @since 2.8
 */

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */

function AffiliateWPFieldText(_ref) {
  var attributes = _ref.attributes,
    setAttributes = _ref.setAttributes,
    isSelected = _ref.isSelected,
    name = _ref.name,
    context = _ref.context,
    clientId = _ref.clientId;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_components_text_edit__WEBPACK_IMPORTED_MODULE_3__["default"], {
    attributes: attributes,
    setAttributes: setAttributes,
    isSelected: isSelected,
    name: name,
    context: context
  });
}
/* harmony default export */ __webpack_exports__["default"] = (AffiliateWPFieldText);

/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/text/index.js":
/*!*******************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/text/index.js ***!
  \*******************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit */ "./assets/js/editor/blocks/registration/fields/text/edit.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);

/**
 * Affiliate registration text field Block.
 *
 * @since 2.8
 */

/**
 * Internal dependencies
 */


/**
 * WordPress dependencies
 */


var icon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Icon"], {
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    fill: "none",
    viewBox: "0 0 24 24",
    stroke: "currentColor"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: 2,
    d: "M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z",
    fill: "none"
  }))
});
var name = 'affiliatewp/field-text';
var settings = {
  /* translators: block name */
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Text', 'affiliate-wp'),
  category: 'affiliatewp',
  parent: ['affiliatewp/registration'],
  icon: icon,
  attributes: {
    label: {
      type: 'string',
      default: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Text', 'affiliate-wp')
    },
    required: {
      type: 'boolean',
      default: false
    },
    placeholder: {
      type: 'string'
    },
    type: {
      type: 'string'
    }
  },
  /* translators: block description */
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('A field for collecting text.', 'affiliate-wp'),
  keywords: ['affiliatewp', /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('text', 'affiliatewp')],
  supports: {
    html: false,
    lightBlockWrapper: true
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_1__["default"],
  save: function save() {
    return null;
  }
};


/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/textarea/edit.js":
/*!**********************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/textarea/edit.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "./node_modules/@babel/runtime/helpers/slicedToArray.js");
/* harmony import */ var _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _components_field_label__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../components/field-label */ "./assets/js/editor/blocks/registration/components/field-label.js");
/* harmony import */ var _helpers__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../helpers */ "./assets/js/editor/blocks/registration/helpers.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__);


/**
 * Affiliate Registration Form textarea Edit Component.
 *
 * @since 2.8
 */

/**
 * External dependencies
 */


/**
 * Internal dependencies
 */


/**
 * WordPress dependencies
 */




function AffiliateWPFieldTextArea(_ref) {
  var attributes = _ref.attributes,
    setAttributes = _ref.setAttributes,
    isSelected = _ref.isSelected,
    name = _ref.name,
    context = _ref.context,
    clientId = _ref.clientId;
  var required = attributes.required,
    label = attributes.label,
    placeholder = attributes.placeholder,
    type = attributes.type;
  var _useState = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["useState"])('promotionMethod' === type ? true : false),
    _useState2 = _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0___default()(_useState, 2),
    promotionMethod = _useState2[0],
    setPromotionMethod = _useState2[1];
  var fieldClassNames = classnames__WEBPACK_IMPORTED_MODULE_2___default()('affwp-field', 'affwp-field-textarea');
  var blockProps = Object(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__["useBlockProps"])();
  var showPlaceholders = context['affiliatewp/placeholders'];
  Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["useEffect"])(function () {
    setAttributes({
      type: promotionMethod ? 'promotionMethod' : undefined
    });
  }, [promotionMethod]);
  var disabled = !Object(_helpers__WEBPACK_IMPORTED_MODULE_4__["isCurrentRegistrationBlockChild"])('promotionMethod', clientId) && Object(_helpers__WEBPACK_IMPORTED_MODULE_4__["isRegistrationBlockChild"])('promotionMethod', clientId);
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Field settings', 'affiliate-wp')
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["ToggleControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Required', 'affiliate-wp'),
    className: "affwp-field-label__required",
    checked: required,
    onChange: function onChange(boolean) {
      return setAttributes({
        required: boolean
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["ToggleControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Use as Promotion Method field', 'affiliate-wp'),
    checked: promotionMethod,
    onChange: function onChange(boolean) {
      return setPromotionMethod(boolean);
    },
    disabled: disabled
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Field Label', 'affiliate-wp'),
    value: label,
    onChange: function onChange(label) {
      return setAttributes({
        label: label
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Field Placeholder', 'affiliate-wp'),
    value: placeholder,
    onChange: function onChange(placeholder) {
      return setAttributes({
        placeholder: placeholder
      });
    }
  }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", blockProps, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_components_field_label__WEBPACK_IMPORTED_MODULE_3__["default"], {
    identifier: "label",
    required: required,
    requiredAttribute: 'required',
    label: label,
    labelAttribute: 'label',
    setAttributes: setAttributes,
    isSelected: isSelected,
    name: name,
    context: context
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__["PlainText"], {
    placeholder: showPlaceholders ? Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Add placeholder text…') : '',
    className: fieldClassNames,
    value: placeholder,
    onChange: function onChange(placeholder) {
      return setAttributes({
        placeholder: placeholder
      });
    },
    rows: 5
  })));
}
/* harmony default export */ __webpack_exports__["default"] = (AffiliateWPFieldTextArea);

/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/textarea/index.js":
/*!***********************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/textarea/index.js ***!
  \***********************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit */ "./assets/js/editor/blocks/registration/fields/textarea/edit.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);

/**
 * Affiliate registration textarea field Block.
 *
 * @since 2.8
 */

/**
 * Internal dependencies
 */


/**
 * WordPress dependencies
 */


var icon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Icon"], {
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    fill: "none",
    viewBox: "0 0 24 24",
    stroke: "currentColor"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: 2,
    d: "M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z",
    fill: "none"
  }))
});
var name = 'affiliatewp/field-textarea';
var settings = {
  /* translators: block name */
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Textarea', 'affiliate-wp'),
  category: 'affiliatewp',
  parent: ['affiliatewp/registration'],
  icon: icon,
  attributes: {
    label: {
      type: 'string',
      default: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Message', 'affiliate-wp')
    },
    required: {
      type: 'boolean',
      default: false
    },
    placeholder: {
      type: 'string'
    },
    type: {
      type: 'string'
    }
  },
  /* translators: block description */
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('A field for collecting larger text responses.', 'affiliate-wp'),
  keywords: ['affiliatewp', /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('textarea', 'affiliate-wp')],
  supports: {
    reusable: false,
    html: false,
    lightBlockWrapper: true
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_1__["default"],
  save: function save() {
    return null;
  }
};


/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/username/edit.js":
/*!**********************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/username/edit.js ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _components_text_edit__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../components/text-edit */ "./assets/js/editor/blocks/registration/components/text-edit.js");

/**
 * Affiliate Registration Form username field Edit Component.
 *
 * @since 2.8
 */

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */

function AffiliateWPFieldText(_ref) {
  var attributes = _ref.attributes,
    setAttributes = _ref.setAttributes,
    isSelected = _ref.isSelected,
    name = _ref.name,
    context = _ref.context,
    clientId = _ref.clientId;
  /* translators: Username help text */
  var helpText = Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('The Username field is always required', 'affiliate-wp');
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_components_text_edit__WEBPACK_IMPORTED_MODULE_3__["default"], {
    attributes: attributes,
    setAttributes: setAttributes,
    isSelected: isSelected,
    disableRequired: true,
    help: helpText,
    name: name,
    context: context
  });
}
/* harmony default export */ __webpack_exports__["default"] = (AffiliateWPFieldText);

/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/username/index.js":
/*!***********************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/username/index.js ***!
  \***********************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit */ "./assets/js/editor/blocks/registration/fields/username/edit.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);

/**
 * Affiliate registration username field Block.
 *
 * @since 2.8
 */

/**
 * Internal dependencies
 */


/**
 * WordPress dependencies
 */


var icon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Icon"], {
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    fill: "none",
    viewBox: "0 0 24 24",
    stroke: "currentColor"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: 2,
    d: "M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z",
    fill: "none"
  }))
});
var name = 'affiliatewp/field-username';
var settings = {
  /* translators: block name */
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Username', 'affiliate-wp'),
  category: 'affiliatewp',
  parent: ['affiliatewp/registration'],
  icon: icon,
  attributes: {
    label: {
      type: 'string',
      default: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Username', 'affiliate-wp')
    },
    placeholder: {
      type: 'string'
    },
    required: {
      type: 'boolean',
      default: true
    },
    type: {
      type: 'string'
    }
  },
  /* translators: block description */
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('The affiliate\'s username.', 'affiliate-wp'),
  keywords: ['affiliatewp', /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('username', 'affiliatewp'), /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('login', 'affiliatewp'), /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('text', 'affiliatewp')],
  supports: {
    html: false,
    lightBlockWrapper: true
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_1__["default"],
  save: function save() {
    return null;
  }
};


/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/website/edit.js":
/*!*********************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/website/edit.js ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "./node_modules/@babel/runtime/helpers/slicedToArray.js");
/* harmony import */ var _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _components_field__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../components/field */ "./assets/js/editor/blocks/registration/components/field.js");
/* harmony import */ var _helpers__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../helpers */ "./assets/js/editor/blocks/registration/helpers.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__);


/**
 * Affiliate Registration Form website field Edit Component.
 *
 * @since 2.8
 */

/**
 * External dependencies
 */


/**
 * Internal dependencies
 */



/**
 * WordPress dependencies
 */




function AffiliateWPFieldWebsite(_ref) {
  var attributes = _ref.attributes,
    setAttributes = _ref.setAttributes,
    isSelected = _ref.isSelected,
    name = _ref.name,
    context = _ref.context,
    clientId = _ref.clientId;
  var required = attributes.required,
    label = attributes.label,
    placeholder = attributes.placeholder,
    type = attributes.type;
  var _useState = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["useState"])('websiteUrl' === type ? true : false),
    _useState2 = _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0___default()(_useState, 2),
    websiteUrl = _useState2[0],
    setWebsiteUrl = _useState2[1];
  var fieldClassNames = classnames__WEBPACK_IMPORTED_MODULE_2___default()('affwp-field', 'affwp-field-website');
  Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["useEffect"])(function () {
    setAttributes({
      type: websiteUrl ? 'websiteUrl' : undefined
    });
  }, [websiteUrl]);
  var disabled = !Object(_helpers__WEBPACK_IMPORTED_MODULE_4__["isCurrentRegistrationBlockChild"])('websiteUrl', clientId) && Object(_helpers__WEBPACK_IMPORTED_MODULE_4__["isRegistrationBlockChild"])('websiteUrl', clientId);
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__["InspectorControls"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["PanelBody"], {
    title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Field settings', 'affiliate-wp'),
    initialOpen: true
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["ToggleControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Required', 'affiliate-wp'),
    className: "affwp-field-label__required",
    checked: required,
    onChange: function onChange(required) {
      return setAttributes({
        required: required
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["ToggleControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Save to affiliate\'s user profile', 'affiliate-wp'),
    checked: websiteUrl,
    onChange: function onChange(boolean) {
      return setWebsiteUrl(boolean);
    },
    disabled: disabled,
    help: disabled ? Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Only one Website can be saved as the "Website" field on the WordPress user profile.', 'affiliate-wp') : Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('The Website will be saved to the "Website" field on the affiliate\'s WordPress user profile.', 'affiliate-wp')
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Field Label', 'affiliate-wp'),
    value: label,
    onChange: function onChange(label) {
      return setAttributes({
        label: label
      });
    }
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_7__["TextControl"], {
    label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_5__["__"])('Field Placeholder', 'affiliate-wp'),
    value: placeholder,
    onChange: function onChange(placeholder) {
      return setAttributes({
        placeholder: placeholder
      });
    }
  }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_components_field__WEBPACK_IMPORTED_MODULE_3__["default"], {
    label: label,
    type: "url",
    required: required,
    setAttributes: setAttributes,
    isSelected: isSelected,
    name: name,
    fieldClassNames: fieldClassNames,
    placeholder: placeholder,
    context: context
  }));
}
/* harmony default export */ __webpack_exports__["default"] = (AffiliateWPFieldWebsite);

/***/ }),

/***/ "./assets/js/editor/blocks/registration/fields/website/index.js":
/*!**********************************************************************!*\
  !*** ./assets/js/editor/blocks/registration/fields/website/index.js ***!
  \**********************************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit */ "./assets/js/editor/blocks/registration/fields/website/edit.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);

/**
 * Affiliate registration website field Block.
 *
 * @since 2.8
 */

/**
 * Internal dependencies
 */


/**
 * WordPress dependencies
 */


var icon = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__["Icon"], {
  icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    fill: "none",
    viewBox: "0 0 24 24",
    stroke: "currentColor"
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
    strokeLinecap: "round",
    strokeLinejoin: "round",
    strokeWidth: 2,
    d: "M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9",
    fill: "none"
  }))
});
var name = 'affiliatewp/field-website';
var settings = {
  /* translators: block name */
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Website', 'affiliate-wp'),
  category: 'affiliatewp',
  parent: ['affiliatewp/registration'],
  icon: icon,
  attributes: {
    label: {
      type: 'string',
      default: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('Website', 'affiliate-wp')
    },
    required: {
      type: 'boolean',
      default: false
    },
    placeholder: {
      type: 'string'
    },
    type: {
      type: 'string'
    }
  },
  /* translators: block description */
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('A field for collecting a website URL.', 'affiliate-wp'),
  keywords: ['affiliatewp', /* translators: block keyword */
  Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('url', 'affiliate-wp'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('website', 'affiliate-wp'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])('link', 'affiliate-wp')],
  supports: {
    html: false,
    lightBlockWrapper: true
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_1__["default"],
  save: function save() {
    return null;
  }
};


/***/ }),

/***/ "./assets/js/editor/blocks/registration/helpers.js":
/*!*********************************************************!*\
  !*** ./assets/js/editor/blocks/registration/helpers.js ***!
  \*********************************************************/
/*! exports provided: isRegistrationBlockChild, isCurrentRegistrationBlockChild */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isRegistrationBlockChild", function() { return isRegistrationBlockChild; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isCurrentRegistrationBlockChild", function() { return isCurrentRegistrationBlockChild; });
/**
 * Helper functions for registration fields.
 *
 * @since 2.8
 */

/**
 * isRegistrationBlockChild
 *
 * Determines if a block type is a child of the specified root block.
 *
 * @since 2.8
 *
 * @param type The block type to check
 * @param clientId The clientID passed from the parent block
 * @returns {true|undefined} Returns true if the block type is a child of the specified root block.
 */
var isRegistrationBlockChild = function isRegistrationBlockChild(type, clientId) {
  var blockEditor = wp.data.select('core/block-editor');
  var blockRootClientId = blockEditor.getBlockRootClientId(clientId);
  var innerBlocks = blockEditor.getBlock(blockRootClientId).innerBlocks;
  var block = innerBlocks.find(function (_ref) {
    var attributes = _ref.attributes;
    return attributes.type === type;
  });
  if (block) {
    return block;
  }
  return undefined;
};

/**
 * isCurrentRegistrationBlockChild
 *
 * Determines if the block type is a the current registraiton block.
 *
 * @since 2.8
 *
 * @param type The block type to check
 * @param clientId The clientID passed from the parent block
 * @returns {true|undefined}  Returns true if the block type is a the current registraiton block.
 */
var isCurrentRegistrationBlockChild = function isCurrentRegistrationBlockChild(type, clientId) {
  var _isRegistrationBlockC;
  if (clientId === ((_isRegistrationBlockC = isRegistrationBlockChild(type, clientId)) === null || _isRegistrationBlockC === void 0 ? void 0 : _isRegistrationBlockC.clientId)) {
    return true;
  }
  return false;
};

/***/ }),

/***/ "./assets/js/editor/blocks/registration/index.js":
/*!*******************************************************!*\
  !*** ./assets/js/editor/blocks/registration/index.js ***!
  \*******************************************************/
/*! exports provided: name, settings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "name", function() { return name; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "settings", function() { return settings; });
/* harmony import */ var _components_icon__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../components/icon */ "./assets/js/editor/components/icon.js");
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./edit */ "./assets/js/editor/blocks/registration/edit.js");
/* harmony import */ var _save__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./save */ "./assets/js/editor/blocks/registration/save.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/**
 * Affiliate registration form block.
 *
 * @since 2.8
 */

/**
 * Internal Dependencies
 */




/**
 * WordPress Dependencies
 */

var name = 'affiliatewp/registration';
var settings = {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Affiliate Registration', 'affiliate-wp'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Allow your affiliates to register.', 'affiliate-wp'),
  keywords: [Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Registration', 'affiliate-wp'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Form', 'affiliate-wp'), Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Register', 'affiliate-wp')],
  category: 'affiliatewp',
  icon: _components_icon__WEBPACK_IMPORTED_MODULE_0__["default"],
  supports: {
    html: false,
    lightBlockWrapper: true
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_1__["default"],
  save: _save__WEBPACK_IMPORTED_MODULE_2__["default"]
};


/***/ }),

/***/ "./assets/js/editor/blocks/registration/save.js":
/*!******************************************************!*\
  !*** ./assets/js/editor/blocks/registration/save.js ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return save; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__);

/**
 * Affiliate registration form save handler
 *
 * @since 2.8
 */

/**
  * WordPress dependencies
*/

function save(_ref) {
  var attributes = _ref.attributes;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__["InnerBlocks"].Content, null);
}

/***/ }),

/***/ "./assets/js/editor/components/affiliate-creative.js":
/*!***********************************************************!*\
  !*** ./assets/js/editor/components/affiliate-creative.js ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_taggedTemplateLiteral__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/taggedTemplateLiteral */ "./node_modules/@babel/runtime/helpers/taggedTemplateLiteral.js");
/* harmony import */ var _babel_runtime_helpers_taggedTemplateLiteral__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_taggedTemplateLiteral__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _utils_referral_url__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../utils/referral-url */ "./assets/js/editor/utils/referral-url.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! classnames */ "./node_modules/classnames/index.js");
/* harmony import */ var classnames__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(classnames__WEBPACK_IMPORTED_MODULE_5__);

var _templateObject;

/**
 * Affiliate Creative Block.
 *
 * @since 2.8
 */

/**
 * Internal dependencies
 */


/**
 * External dependencies
 */




/**
 * Affiliate Creative.
 *
 * Affiliate Creative Component.
 *
 * @returns {JSX.Element} Rendered form component.
 * @constructor
 */
var AffiliateCreative = function AffiliateCreative(_ref) {
  var id = _ref.id,
    name = _ref.name,
    description = _ref.description,
    image = _ref.image,
    url = _ref.url,
    text = _ref.text,
    preview = _ref.preview;
  var referralUrl = Object(_utils_referral_url__WEBPACK_IMPORTED_MODULE_2__["default"])({
    url: url,
    format: affwp_blocks.referral_format,
    pretty: affwp_blocks.pretty_referral_urls
  });
  var code = String.raw(_templateObject || (_templateObject = _babel_runtime_helpers_taggedTemplateLiteral__WEBPACK_IMPORTED_MODULE_0___default()(["<a href=\"", "\" title=\"", "\">", "</a>"])), referralUrl, text, text);
  var classes = classnames__WEBPACK_IMPORTED_MODULE_5___default()('affwp-creative', 'creative-' + id);
  var ImageOrText = function ImageOrText() {
    if (image) {
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("img", {
        alt: text,
        src: image
      });
    } else {
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, text);
    }
  };
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("div", {
    className: classes
  }, description && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("p", {
    className: "affwp-creative-desc"
  }, description), preview && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["Disabled"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("p", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("a", {
    href: referralUrl,
    title: text
  }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])(ImageOrText, null)))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("p", null, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Copy and paste the following:', 'affiliate-wp')), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("pre", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__["createElement"])("code", null, code))));
};
/* harmony default export */ __webpack_exports__["default"] = (AffiliateCreative);

/***/ }),

/***/ "./assets/js/editor/components/icon.js":
/*!*********************************************!*\
  !*** ./assets/js/editor/components/icon.js ***!
  \*********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);

/**
 * Block Icon Component.
 *
 * @since 2.8
 */

/**
 * WordPress Dependencies
 */


/**
 * Block Icon.
 *
 * Renders the AffiliateWP Logo.
 *
 * @returns {JSX.Element} Rendered form component.
 * @constructor
 */
var blockIcon = function blockIcon(_ref) {
  var color = _ref.color;
  var fill = color ? '#E34F43' : '';
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Icon"], {
    icon: Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("svg", {
      width: "24",
      height: "24",
      viewBox: "0 0 24 24"
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("path", {
      d: "M19.3657 14.051C19.1474 14.051 18.9334 14.0722 18.7215 14.1019L15.668 8.60516C16.3419 7.79993 16.7487 6.76373 16.7487 5.63429C16.7508 3.07876 14.6721 1 12.1165 1C9.56101 1 7.48013 3.07876 7.48013 5.63429C7.48013 6.74254 7.87215 7.75967 8.52269 8.55642L5.33993 14.1104C5.10895 14.0743 4.87586 14.051 4.63429 14.051C2.07876 14.051 0 16.1298 0 18.6853C0 21.2409 2.07876 23.3196 4.63429 23.3196C7.18983 23.3196 9.26859 21.2409 9.26859 18.6853C9.26859 17.5771 8.87657 16.5599 8.22603 15.7632L11.4088 10.2114C11.6398 10.2474 11.8729 10.2707 12.1144 10.2707C12.3327 10.2707 12.5467 10.2495 12.7586 10.2198L15.8121 15.7145C15.1383 16.5197 14.7314 17.5559 14.7314 18.6853C14.7314 21.2409 16.8102 23.3196 19.3657 23.3196C21.9212 23.3196 24 21.2409 24 18.6853C24 16.1298 21.9212 14.051 19.3657 14.051ZM12.1165 4.04715C12.9917 4.04715 13.7037 4.75914 13.7037 5.63429C13.7037 6.50945 12.9917 7.22144 12.1165 7.22144C11.2414 7.22144 10.5294 6.50945 10.5294 5.63429C10.5294 4.75914 11.2414 4.04715 12.1165 4.04715ZM4.63429 20.2725C3.75914 20.2725 3.04715 19.5605 3.04715 18.6853C3.04715 17.8102 3.75914 17.0982 4.63429 17.0982C5.50945 17.0982 6.22144 17.8102 6.22144 18.6853C6.22144 19.5605 5.50945 20.2725 4.63429 20.2725ZM19.3657 20.2725C18.4906 20.2725 17.7786 19.5605 17.7786 18.6853C17.7786 17.8102 18.4906 17.0982 19.3657 17.0982C20.2409 17.0982 20.9529 17.8102 20.9529 18.6853C20.9529 19.5605 20.2409 20.2725 19.3657 20.2725Z",
      fill: fill
    }))
  });
};
/* harmony default export */ __webpack_exports__["default"] = (blockIcon);

/***/ }),

/***/ "./assets/js/editor/index.js":
/*!***********************************!*\
  !*** ./assets/js/editor/index.js ***!
  \***********************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _blocks_affiliate_area__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./blocks/affiliate-area */ "./assets/js/editor/blocks/affiliate-area/index.js");
/* harmony import */ var _blocks_registration__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./blocks/registration */ "./assets/js/editor/blocks/registration/index.js");
/* harmony import */ var _blocks_login__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./blocks/login */ "./assets/js/editor/blocks/login/index.js");
/* harmony import */ var _blocks_affiliate_content__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./blocks/affiliate-content */ "./assets/js/editor/blocks/affiliate-content/index.js");
/* harmony import */ var _blocks_non_affiliate_content__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./blocks/non-affiliate-content */ "./assets/js/editor/blocks/non-affiliate-content/index.js");
/* harmony import */ var _blocks_opt_in__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./blocks/opt-in */ "./assets/js/editor/blocks/opt-in/index.js");
/* harmony import */ var _blocks_affiliate_referral_url__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./blocks/affiliate-referral-url */ "./assets/js/editor/blocks/affiliate-referral-url/index.js");
/* harmony import */ var _blocks_affiliate_creatives__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./blocks/affiliate-creatives */ "./assets/js/editor/blocks/affiliate-creatives/index.js");
/* harmony import */ var _blocks_affiliate_creative__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./blocks/affiliate-creative */ "./assets/js/editor/blocks/affiliate-creative/index.js");
/* harmony import */ var _blocks_registration_fields_text__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./blocks/registration/fields/text */ "./assets/js/editor/blocks/registration/fields/text/index.js");
/* harmony import */ var _blocks_registration_fields_name__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./blocks/registration/fields/name */ "./assets/js/editor/blocks/registration/fields/name/index.js");
/* harmony import */ var _blocks_registration_fields_username__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./blocks/registration/fields/username */ "./assets/js/editor/blocks/registration/fields/username/index.js");
/* harmony import */ var _blocks_registration_fields_payment_email__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./blocks/registration/fields/payment-email */ "./assets/js/editor/blocks/registration/fields/payment-email/index.js");
/* harmony import */ var _blocks_registration_fields_account_email__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./blocks/registration/fields/account-email */ "./assets/js/editor/blocks/registration/fields/account-email/index.js");
/* harmony import */ var _blocks_registration_fields_textarea__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./blocks/registration/fields/textarea */ "./assets/js/editor/blocks/registration/fields/textarea/index.js");
/* harmony import */ var _blocks_registration_fields_email__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./blocks/registration/fields/email */ "./assets/js/editor/blocks/registration/fields/email/index.js");
/* harmony import */ var _blocks_registration_fields_website__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./blocks/registration/fields/website */ "./assets/js/editor/blocks/registration/fields/website/index.js");
/* harmony import */ var _blocks_registration_fields_checkbox__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./blocks/registration/fields/checkbox */ "./assets/js/editor/blocks/registration/fields/checkbox/index.js");
/* harmony import */ var _blocks_registration_fields_password__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ./blocks/registration/fields/password */ "./assets/js/editor/blocks/registration/fields/password/index.js");
/* harmony import */ var _blocks_registration_fields_phone__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./blocks/registration/fields/phone */ "./assets/js/editor/blocks/registration/fields/phone/index.js");
/* harmony import */ var _blocks_registration_fields_register_button__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ./blocks/registration/fields/register-button */ "./assets/js/editor/blocks/registration/fields/register-button/index.js");
/* harmony import */ var _blocks_registration_fields_terms_of_use__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! ./blocks/registration/fields/terms-of-use */ "./assets/js/editor/blocks/registration/fields/terms-of-use/index.js");
/* harmony import */ var _blocks_registration_fields_select__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! ./blocks/registration/fields/select */ "./assets/js/editor/blocks/registration/fields/select/index.js");
/* harmony import */ var _blocks_registration_fields_radio__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(/*! ./blocks/registration/fields/radio */ "./assets/js/editor/blocks/registration/fields/radio/index.js");
/* harmony import */ var _blocks_registration_fields_checkbox_multiple__WEBPACK_IMPORTED_MODULE_25__ = __webpack_require__(/*! ./blocks/registration/fields/checkbox-multiple */ "./assets/js/editor/blocks/registration/fields/checkbox-multiple/index.js");
/**
 * WordPress Dependencies
 */


/**
 * Internal Dependencies
 */

























var registerBlocks = function registerBlocks() {
  [_blocks_affiliate_area__WEBPACK_IMPORTED_MODULE_1__, _blocks_registration__WEBPACK_IMPORTED_MODULE_2__, _blocks_login__WEBPACK_IMPORTED_MODULE_3__, _blocks_affiliate_content__WEBPACK_IMPORTED_MODULE_4__, _blocks_non_affiliate_content__WEBPACK_IMPORTED_MODULE_5__, _blocks_opt_in__WEBPACK_IMPORTED_MODULE_6__, _blocks_affiliate_referral_url__WEBPACK_IMPORTED_MODULE_7__, _blocks_affiliate_creatives__WEBPACK_IMPORTED_MODULE_8__, _blocks_affiliate_creative__WEBPACK_IMPORTED_MODULE_9__, _blocks_registration_fields_email__WEBPACK_IMPORTED_MODULE_16__, _blocks_registration_fields_text__WEBPACK_IMPORTED_MODULE_10__, _blocks_registration_fields_textarea__WEBPACK_IMPORTED_MODULE_15__, _blocks_registration_fields_website__WEBPACK_IMPORTED_MODULE_17__, _blocks_registration_fields_password__WEBPACK_IMPORTED_MODULE_19__, _blocks_registration_fields_phone__WEBPACK_IMPORTED_MODULE_20__, _blocks_registration_fields_checkbox__WEBPACK_IMPORTED_MODULE_18__, _blocks_registration_fields_name__WEBPACK_IMPORTED_MODULE_11__, _blocks_registration_fields_username__WEBPACK_IMPORTED_MODULE_12__, _blocks_registration_fields_payment_email__WEBPACK_IMPORTED_MODULE_13__, _blocks_registration_fields_account_email__WEBPACK_IMPORTED_MODULE_14__, _blocks_registration_fields_register_button__WEBPACK_IMPORTED_MODULE_21__, _blocks_registration_fields_terms_of_use__WEBPACK_IMPORTED_MODULE_22__, _blocks_registration_fields_select__WEBPACK_IMPORTED_MODULE_23__, _blocks_registration_fields_radio__WEBPACK_IMPORTED_MODULE_24__, _blocks_registration_fields_checkbox_multiple__WEBPACK_IMPORTED_MODULE_25__].forEach(function (_ref) {
    var name = _ref.name,
      settings = _ref.settings;
    Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__["registerBlockType"])(name, settings);
  });
};
registerBlocks();

/***/ }),

/***/ "./assets/js/editor/utils/referral-url.js":
/*!************************************************!*\
  !*** ./assets/js/editor/utils/referral-url.js ***!
  \************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "./node_modules/@babel/runtime/helpers/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/url */ "@wordpress/url");
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_url__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var trailing_slash_it__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! trailing-slash-it */ "./node_modules/trailing-slash-it/build/index.js");
/* harmony import */ var trailing_slash_it__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(trailing_slash_it__WEBPACK_IMPORTED_MODULE_3__);

/**
 * Referral URL Function.
 *
 * Helper function to generate a referral URL.
 *
 * @since 2.8
 */

/**
 * External dependencies
 */




/**
 * ReferralUrl.
 *
 * Generates a referral URL.
 *
 * @since 2.8
 *
 * @param {string} url    The URL to convert to a referral URL.
 * @param {string} format The referral format. Can be "id" or "username". Defaults to "id".
 * @param {string} pretty Format the URL as pretty. Leave empty to use non-pretty URLs.
 *                        Can be "yes", "default", or undefined. If "yes", this will make the URL pretty. If "default"
 *                        This will use the default settings set in AffiliateWP. If undefined, this will make the url
 *                        non-pretty. Default Undefined (non-pretty URL).
 * @returns {string}      The referral URL.
 */
function referralUrl(_ref) {
  var url = _ref.url,
    format = _ref.format,
    pretty = _ref.pretty;
  // The global "Default Referral Format" setting. Either "username" or "ID".
  var referralFormat = affwp_blocks.referral_format;

  // The global "Pretty Affiliate URLs" option.
  var prettyAffiliateUrls = affwp_blocks.pretty_referral_urls;

  /**
   * Get the affiliate ID of the currently logged in user. If they are not an
   * affiliate, we'll show a demo ID.
   */
  var affiliateId = affwp_blocks.affiliate_id || Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('123', 'affiliate-wp');

  /**
   * Get the affiliate username of the currently logged in user. If they are not an
   * affiliate, we'll show a demo username.
   */
  var affiliateUsername = affwp_blocks.affiliate_username || Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('demoaffiliate', 'affiliate-wp');

  /**
   * Get the referral variable. E.g. "ref".
   */
  var referralVariable = affwp_blocks.referral_variable;

  /**
   * Get the permalink. If no custom URL has been entered it will fall back
   * to the current page's permalink.
   */
  var permalink = url ? Object(trailing_slash_it__WEBPACK_IMPORTED_MODULE_3__["trailingSlashIt"])(url) : wp.data.select('core/editor').getPermalink();
  var referralFormatValue = '';

  // "Site Default" option selected
  if ('default' === format) {
    switch (referralFormat) {
      case 'username':
        referralFormatValue = affiliateUsername;
        break;
      case 'id':
      default:
        referralFormatValue = affiliateId;
        break;
    }
  } else if ('id' === format) {
    referralFormatValue = affiliateId;
  } else if ('username' === format) {
    referralFormatValue = affiliateUsername;
  }

  // Build the referral URL to show.
  var referralURL = Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_2__["addQueryArgs"])(permalink, _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0___default()({}, referralVariable, referralFormatValue));
  var isPrettyAffiliateURLs = false;
  if ('default' === pretty) {
    // Check that the site default is currently set.
    if (prettyAffiliateUrls) {
      isPrettyAffiliateURLs = true;
    }
  }

  // Explicitly enabled.
  if ('yes' === pretty) {
    isPrettyAffiliateURLs = true;
  }
  if (isPrettyAffiliateURLs) {
    referralURL = "".concat(permalink).concat(Object(trailing_slash_it__WEBPACK_IMPORTED_MODULE_3__["trailingSlashIt"])(referralVariable)).concat(Object(trailing_slash_it__WEBPACK_IMPORTED_MODULE_3__["trailingSlashIt"])(referralFormatValue));
  }
  return referralURL;
}
/* harmony default export */ __webpack_exports__["default"] = (referralUrl);

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/arrayLikeToArray.js":
/*!*****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/arrayLikeToArray.js ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _arrayLikeToArray(arr, len) {
  if (len == null || len > arr.length) len = arr.length;
  for (var i = 0, arr2 = new Array(len); i < len; i++) {
    arr2[i] = arr[i];
  }
  return arr2;
}
module.exports = _arrayLikeToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/arrayWithHoles.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/arrayWithHoles.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _arrayWithHoles(arr) {
  if (Array.isArray(arr)) return arr;
}
module.exports = _arrayWithHoles, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/arrayWithoutHoles.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/arrayWithoutHoles.js ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var arrayLikeToArray = __webpack_require__(/*! ./arrayLikeToArray.js */ "./node_modules/@babel/runtime/helpers/arrayLikeToArray.js");
function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) return arrayLikeToArray(arr);
}
module.exports = _arrayWithoutHoles, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/assertThisInitialized.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/assertThisInitialized.js ***!
  \**********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _assertThisInitialized(self) {
  if (self === void 0) {
    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  }
  return self;
}
module.exports = _assertThisInitialized, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/asyncToGenerator.js":
/*!*****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/asyncToGenerator.js ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) {
  try {
    var info = gen[key](arg);
    var value = info.value;
  } catch (error) {
    reject(error);
    return;
  }
  if (info.done) {
    resolve(value);
  } else {
    Promise.resolve(value).then(_next, _throw);
  }
}
function _asyncToGenerator(fn) {
  return function () {
    var self = this,
      args = arguments;
    return new Promise(function (resolve, reject) {
      var gen = fn.apply(self, args);
      function _next(value) {
        asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value);
      }
      function _throw(err) {
        asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err);
      }
      _next(undefined);
    });
  };
}
module.exports = _asyncToGenerator, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/classCallCheck.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/classCallCheck.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}
module.exports = _classCallCheck, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/createClass.js":
/*!************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/createClass.js ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _defineProperties(target, props) {
  for (var i = 0; i < props.length; i++) {
    var descriptor = props[i];
    descriptor.enumerable = descriptor.enumerable || false;
    descriptor.configurable = true;
    if ("value" in descriptor) descriptor.writable = true;
    Object.defineProperty(target, descriptor.key, descriptor);
  }
}
function _createClass(Constructor, protoProps, staticProps) {
  if (protoProps) _defineProperties(Constructor.prototype, protoProps);
  if (staticProps) _defineProperties(Constructor, staticProps);
  Object.defineProperty(Constructor, "prototype", {
    writable: false
  });
  return Constructor;
}
module.exports = _createClass, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/defineProperty.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/defineProperty.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }
  return obj;
}
module.exports = _defineProperty, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/getPrototypeOf.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/getPrototypeOf.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _getPrototypeOf(o) {
  module.exports = _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function _getPrototypeOf(o) {
    return o.__proto__ || Object.getPrototypeOf(o);
  }, module.exports.__esModule = true, module.exports["default"] = module.exports;
  return _getPrototypeOf(o);
}
module.exports = _getPrototypeOf, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/inherits.js":
/*!*********************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/inherits.js ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var setPrototypeOf = __webpack_require__(/*! ./setPrototypeOf.js */ "./node_modules/@babel/runtime/helpers/setPrototypeOf.js");
function _inherits(subClass, superClass) {
  if (typeof superClass !== "function" && superClass !== null) {
    throw new TypeError("Super expression must either be null or a function");
  }
  subClass.prototype = Object.create(superClass && superClass.prototype, {
    constructor: {
      value: subClass,
      writable: true,
      configurable: true
    }
  });
  Object.defineProperty(subClass, "prototype", {
    writable: false
  });
  if (superClass) setPrototypeOf(subClass, superClass);
}
module.exports = _inherits, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/iterableToArray.js":
/*!****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/iterableToArray.js ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _iterableToArray(iter) {
  if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter);
}
module.exports = _iterableToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/iterableToArrayLimit.js":
/*!*********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/iterableToArrayLimit.js ***!
  \*********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _iterableToArrayLimit(arr, i) {
  var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"];
  if (_i == null) return;
  var _arr = [];
  var _n = true;
  var _d = false;
  var _s, _e;
  try {
    for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) {
      _arr.push(_s.value);
      if (i && _arr.length === i) break;
    }
  } catch (err) {
    _d = true;
    _e = err;
  } finally {
    try {
      if (!_n && _i["return"] != null) _i["return"]();
    } finally {
      if (_d) throw _e;
    }
  }
  return _arr;
}
module.exports = _iterableToArrayLimit, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/nonIterableRest.js":
/*!****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/nonIterableRest.js ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
module.exports = _nonIterableRest, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/nonIterableSpread.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/nonIterableSpread.js ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
module.exports = _nonIterableSpread, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var _typeof = __webpack_require__(/*! ./typeof.js */ "./node_modules/@babel/runtime/helpers/typeof.js")["default"];
var assertThisInitialized = __webpack_require__(/*! ./assertThisInitialized.js */ "./node_modules/@babel/runtime/helpers/assertThisInitialized.js");
function _possibleConstructorReturn(self, call) {
  if (call && (_typeof(call) === "object" || typeof call === "function")) {
    return call;
  } else if (call !== void 0) {
    throw new TypeError("Derived constructors may only return object or undefined");
  }
  return assertThisInitialized(self);
}
module.exports = _possibleConstructorReturn, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/setPrototypeOf.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/setPrototypeOf.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _setPrototypeOf(o, p) {
  module.exports = _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function _setPrototypeOf(o, p) {
    o.__proto__ = p;
    return o;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports;
  return _setPrototypeOf(o, p);
}
module.exports = _setPrototypeOf, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/slicedToArray.js":
/*!**************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/slicedToArray.js ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var arrayWithHoles = __webpack_require__(/*! ./arrayWithHoles.js */ "./node_modules/@babel/runtime/helpers/arrayWithHoles.js");
var iterableToArrayLimit = __webpack_require__(/*! ./iterableToArrayLimit.js */ "./node_modules/@babel/runtime/helpers/iterableToArrayLimit.js");
var unsupportedIterableToArray = __webpack_require__(/*! ./unsupportedIterableToArray.js */ "./node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js");
var nonIterableRest = __webpack_require__(/*! ./nonIterableRest.js */ "./node_modules/@babel/runtime/helpers/nonIterableRest.js");
function _slicedToArray(arr, i) {
  return arrayWithHoles(arr) || iterableToArrayLimit(arr, i) || unsupportedIterableToArray(arr, i) || nonIterableRest();
}
module.exports = _slicedToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/taggedTemplateLiteral.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/taggedTemplateLiteral.js ***!
  \**********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _taggedTemplateLiteral(strings, raw) {
  if (!raw) {
    raw = strings.slice(0);
  }
  return Object.freeze(Object.defineProperties(strings, {
    raw: {
      value: Object.freeze(raw)
    }
  }));
}
module.exports = _taggedTemplateLiteral, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/toConsumableArray.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/toConsumableArray.js ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var arrayWithoutHoles = __webpack_require__(/*! ./arrayWithoutHoles.js */ "./node_modules/@babel/runtime/helpers/arrayWithoutHoles.js");
var iterableToArray = __webpack_require__(/*! ./iterableToArray.js */ "./node_modules/@babel/runtime/helpers/iterableToArray.js");
var unsupportedIterableToArray = __webpack_require__(/*! ./unsupportedIterableToArray.js */ "./node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js");
var nonIterableSpread = __webpack_require__(/*! ./nonIterableSpread.js */ "./node_modules/@babel/runtime/helpers/nonIterableSpread.js");
function _toConsumableArray(arr) {
  return arrayWithoutHoles(arr) || iterableToArray(arr) || unsupportedIterableToArray(arr) || nonIterableSpread();
}
module.exports = _toConsumableArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/typeof.js":
/*!*******************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/typeof.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _typeof(obj) {
  "@babel/helpers - typeof";

  return (module.exports = _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) {
    return typeof obj;
  } : function (obj) {
    return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports), _typeof(obj);
}
module.exports = _typeof, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js":
/*!***************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js ***!
  \***************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var arrayLikeToArray = __webpack_require__(/*! ./arrayLikeToArray.js */ "./node_modules/@babel/runtime/helpers/arrayLikeToArray.js");
function _unsupportedIterableToArray(o, minLen) {
  if (!o) return;
  if (typeof o === "string") return arrayLikeToArray(o, minLen);
  var n = Object.prototype.toString.call(o).slice(8, -1);
  if (n === "Object" && o.constructor) n = o.constructor.name;
  if (n === "Map" || n === "Set") return Array.from(o);
  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return arrayLikeToArray(o, minLen);
}
module.exports = _unsupportedIterableToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "./node_modules/classnames/index.js":
/*!******************************************!*\
  !*** ./node_modules/classnames/index.js ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
	Copyright (c) 2018 Jed Watson.
	Licensed under the MIT License (MIT), see
	http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;
	var nativeCodeString = '[native code]';

	function classNames() {
		var classes = [];

		for (var i = 0; i < arguments.length; i++) {
			var arg = arguments[i];
			if (!arg) continue;

			var argType = typeof arg;

			if (argType === 'string' || argType === 'number') {
				classes.push(arg);
			} else if (Array.isArray(arg)) {
				if (arg.length) {
					var inner = classNames.apply(null, arg);
					if (inner) {
						classes.push(inner);
					}
				}
			} else if (argType === 'object') {
				if (arg.toString !== Object.prototype.toString && !arg.toString.toString().includes('[native code]')) {
					classes.push(arg.toString());
					continue;
				}

				for (var key in arg) {
					if (hasOwn.call(arg, key) && arg[key]) {
						classes.push(key);
					}
				}
			}
		}

		return classes.join(' ');
	}

	if ( true && module.exports) {
		classNames.default = classNames;
		module.exports = classNames;
	} else if (true) {
		// register as 'classnames', consistent with npm package name
		!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = (function () {
			return classNames;
		}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	} else {}
}());


/***/ }),

/***/ "./node_modules/trailing-slash-it/build/index.js":
/*!*******************************************************!*\
  !*** ./node_modules/trailing-slash-it/build/index.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * Removes trailing forward slashes if they exist.
 *
 * If the string doesn't end with a slash, we simply return it.
 */
var unTrailingSlashIt = function unTrailingSlashIt(str) {
  if (str.endsWith('/') || str.endsWith('\\')) {
    return unTrailingSlashIt(str.slice(0, -1));
  }

  return str;
};

/**
 * Appends a trailing slash.
 *
 * Will remove a trailing forward slash if it exists already, before adding a
 * trailing forward slash. This prevents double slashing a string or path.
 */
var trailingSlashIt = function trailingSlashIt(str) {
  return unTrailingSlashIt(str) + '/';
};

module.exports = trailingSlashIt;
module.exports.trailingSlashIt = trailingSlashIt;
module.exports.unTrailingSlashIt = unTrailingSlashIt;

/***/ }),

/***/ "@babel/runtime/regenerator":
/*!*************************************!*\
  !*** external "regeneratorRuntime" ***!
  \*************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["regeneratorRuntime"]; }());

/***/ }),

/***/ "@wordpress/api-fetch":
/*!**********************************!*\
  !*** external ["wp","apiFetch"] ***!
  \**********************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["apiFetch"]; }());

/***/ }),

/***/ "@wordpress/block-editor":
/*!*************************************!*\
  !*** external ["wp","blockEditor"] ***!
  \*************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["blockEditor"]; }());

/***/ }),

/***/ "@wordpress/blocks":
/*!********************************!*\
  !*** external ["wp","blocks"] ***!
  \********************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["blocks"]; }());

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["components"]; }());

/***/ }),

/***/ "@wordpress/compose":
/*!*********************************!*\
  !*** external ["wp","compose"] ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["compose"]; }());

/***/ }),

/***/ "@wordpress/data":
/*!******************************!*\
  !*** external ["wp","data"] ***!
  \******************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["data"]; }());

/***/ }),

/***/ "@wordpress/editor":
/*!********************************!*\
  !*** external ["wp","editor"] ***!
  \********************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["editor"]; }());

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["element"]; }());

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["i18n"]; }());

/***/ }),

/***/ "@wordpress/url":
/*!*****************************!*\
  !*** external ["wp","url"] ***!
  \*****************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["url"]; }());

/***/ })

/******/ });
//# sourceMappingURL=index.js.map