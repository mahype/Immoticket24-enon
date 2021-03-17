
(function(l, r) { if (l.getElementById('livereloadscript')) return; r = l.createElement('script'); r.async = 1; r.src = '//' + (window.location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1'; r.id = 'livereloadscript'; l.getElementsByTagName('head')[0].appendChild(r) })(window.document);
var app = (function () {
    'use strict';

    function noop() { }
    const identity = x => x;
    function assign(tar, src) {
        // @ts-ignore
        for (const k in src)
            tar[k] = src[k];
        return tar;
    }
    function add_location(element, file, line, column, char) {
        element.__svelte_meta = {
            loc: { file, line, column, char }
        };
    }
    function run(fn) {
        return fn();
    }
    function blank_object() {
        return Object.create(null);
    }
    function run_all(fns) {
        fns.forEach(run);
    }
    function is_function(thing) {
        return typeof thing === 'function';
    }
    function safe_not_equal(a, b) {
        return a != a ? b == b : a !== b || ((a && typeof a === 'object') || typeof a === 'function');
    }
    function is_empty(obj) {
        return Object.keys(obj).length === 0;
    }
    function validate_store(store, name) {
        if (store != null && typeof store.subscribe !== 'function') {
            throw new Error(`'${name}' is not a store with a 'subscribe' method`);
        }
    }
    function subscribe(store, ...callbacks) {
        if (store == null) {
            return noop;
        }
        const unsub = store.subscribe(...callbacks);
        return unsub.unsubscribe ? () => unsub.unsubscribe() : unsub;
    }
    function component_subscribe(component, store, callback) {
        component.$$.on_destroy.push(subscribe(store, callback));
    }
    function null_to_empty(value) {
        return value == null ? '' : value;
    }

    const is_client = typeof window !== 'undefined';
    let now = is_client
        ? () => window.performance.now()
        : () => Date.now();
    let raf = is_client ? cb => requestAnimationFrame(cb) : noop;

    const tasks = new Set();
    function run_tasks(now) {
        tasks.forEach(task => {
            if (!task.c(now)) {
                tasks.delete(task);
                task.f();
            }
        });
        if (tasks.size !== 0)
            raf(run_tasks);
    }
    /**
     * Creates a new task that runs on each raf frame
     * until it returns a falsy value or is aborted
     */
    function loop(callback) {
        let task;
        if (tasks.size === 0)
            raf(run_tasks);
        return {
            promise: new Promise(fulfill => {
                tasks.add(task = { c: callback, f: fulfill });
            }),
            abort() {
                tasks.delete(task);
            }
        };
    }

    function append(target, node) {
        target.appendChild(node);
    }
    function insert(target, node, anchor) {
        target.insertBefore(node, anchor || null);
    }
    function detach(node) {
        node.parentNode.removeChild(node);
    }
    function destroy_each(iterations, detaching) {
        for (let i = 0; i < iterations.length; i += 1) {
            if (iterations[i])
                iterations[i].d(detaching);
        }
    }
    function element(name) {
        return document.createElement(name);
    }
    function text(data) {
        return document.createTextNode(data);
    }
    function space() {
        return text(' ');
    }
    function empty() {
        return text('');
    }
    function listen(node, event, handler, options) {
        node.addEventListener(event, handler, options);
        return () => node.removeEventListener(event, handler, options);
    }
    function prevent_default(fn) {
        return function (event) {
            event.preventDefault();
            // @ts-ignore
            return fn.call(this, event);
        };
    }
    function attr(node, attribute, value) {
        if (value == null)
            node.removeAttribute(attribute);
        else if (node.getAttribute(attribute) !== value)
            node.setAttribute(attribute, value);
    }
    function to_number(value) {
        return value === '' ? null : +value;
    }
    function children(element) {
        return Array.from(element.childNodes);
    }
    function set_input_value(input, value) {
        input.value = value == null ? '' : value;
    }
    function set_style(node, key, value, important) {
        node.style.setProperty(key, value, important ? 'important' : '');
    }
    function select_option(select, value) {
        for (let i = 0; i < select.options.length; i += 1) {
            const option = select.options[i];
            if (option.__value === value) {
                option.selected = true;
                return;
            }
        }
    }
    function select_value(select) {
        const selected_option = select.querySelector(':checked') || select.options[0];
        return selected_option && selected_option.__value;
    }
    function toggle_class(element, name, toggle) {
        element.classList[toggle ? 'add' : 'remove'](name);
    }
    function custom_event(type, detail) {
        const e = document.createEvent('CustomEvent');
        e.initCustomEvent(type, false, false, detail);
        return e;
    }

    const active_docs = new Set();
    let active = 0;
    // https://github.com/darkskyapp/string-hash/blob/master/index.js
    function hash(str) {
        let hash = 5381;
        let i = str.length;
        while (i--)
            hash = ((hash << 5) - hash) ^ str.charCodeAt(i);
        return hash >>> 0;
    }
    function create_rule(node, a, b, duration, delay, ease, fn, uid = 0) {
        const step = 16.666 / duration;
        let keyframes = '{\n';
        for (let p = 0; p <= 1; p += step) {
            const t = a + (b - a) * ease(p);
            keyframes += p * 100 + `%{${fn(t, 1 - t)}}\n`;
        }
        const rule = keyframes + `100% {${fn(b, 1 - b)}}\n}`;
        const name = `__svelte_${hash(rule)}_${uid}`;
        const doc = node.ownerDocument;
        active_docs.add(doc);
        const stylesheet = doc.__svelte_stylesheet || (doc.__svelte_stylesheet = doc.head.appendChild(element('style')).sheet);
        const current_rules = doc.__svelte_rules || (doc.__svelte_rules = {});
        if (!current_rules[name]) {
            current_rules[name] = true;
            stylesheet.insertRule(`@keyframes ${name} ${rule}`, stylesheet.cssRules.length);
        }
        const animation = node.style.animation || '';
        node.style.animation = `${animation ? `${animation}, ` : ''}${name} ${duration}ms linear ${delay}ms 1 both`;
        active += 1;
        return name;
    }
    function delete_rule(node, name) {
        const previous = (node.style.animation || '').split(', ');
        const next = previous.filter(name
            ? anim => anim.indexOf(name) < 0 // remove specific animation
            : anim => anim.indexOf('__svelte') === -1 // remove all Svelte animations
        );
        const deleted = previous.length - next.length;
        if (deleted) {
            node.style.animation = next.join(', ');
            active -= deleted;
            if (!active)
                clear_rules();
        }
    }
    function clear_rules() {
        raf(() => {
            if (active)
                return;
            active_docs.forEach(doc => {
                const stylesheet = doc.__svelte_stylesheet;
                let i = stylesheet.cssRules.length;
                while (i--)
                    stylesheet.deleteRule(i);
                doc.__svelte_rules = {};
            });
            active_docs.clear();
        });
    }

    let current_component;
    function set_current_component(component) {
        current_component = component;
    }
    function get_current_component() {
        if (!current_component)
            throw new Error('Function called outside component initialization');
        return current_component;
    }
    function createEventDispatcher() {
        const component = get_current_component();
        return (type, detail) => {
            const callbacks = component.$$.callbacks[type];
            if (callbacks) {
                // TODO are there situations where events could be dispatched
                // in a server (non-DOM) environment?
                const event = custom_event(type, detail);
                callbacks.slice().forEach(fn => {
                    fn.call(component, event);
                });
            }
        };
    }
    // TODO figure out if we still want to support
    // shorthand events, or if we want to implement
    // a real bubbling mechanism
    function bubble(component, event) {
        const callbacks = component.$$.callbacks[event.type];
        if (callbacks) {
            callbacks.slice().forEach(fn => fn(event));
        }
    }

    const dirty_components = [];
    const binding_callbacks = [];
    const render_callbacks = [];
    const flush_callbacks = [];
    const resolved_promise = Promise.resolve();
    let update_scheduled = false;
    function schedule_update() {
        if (!update_scheduled) {
            update_scheduled = true;
            resolved_promise.then(flush);
        }
    }
    function add_render_callback(fn) {
        render_callbacks.push(fn);
    }
    let flushing = false;
    const seen_callbacks = new Set();
    function flush() {
        if (flushing)
            return;
        flushing = true;
        do {
            // first, call beforeUpdate functions
            // and update components
            for (let i = 0; i < dirty_components.length; i += 1) {
                const component = dirty_components[i];
                set_current_component(component);
                update(component.$$);
            }
            set_current_component(null);
            dirty_components.length = 0;
            while (binding_callbacks.length)
                binding_callbacks.pop()();
            // then, once components are updated, call
            // afterUpdate functions. This may cause
            // subsequent updates...
            for (let i = 0; i < render_callbacks.length; i += 1) {
                const callback = render_callbacks[i];
                if (!seen_callbacks.has(callback)) {
                    // ...so guard against infinite loops
                    seen_callbacks.add(callback);
                    callback();
                }
            }
            render_callbacks.length = 0;
        } while (dirty_components.length);
        while (flush_callbacks.length) {
            flush_callbacks.pop()();
        }
        update_scheduled = false;
        flushing = false;
        seen_callbacks.clear();
    }
    function update($$) {
        if ($$.fragment !== null) {
            $$.update();
            run_all($$.before_update);
            const dirty = $$.dirty;
            $$.dirty = [-1];
            $$.fragment && $$.fragment.p($$.ctx, dirty);
            $$.after_update.forEach(add_render_callback);
        }
    }

    let promise;
    function wait() {
        if (!promise) {
            promise = Promise.resolve();
            promise.then(() => {
                promise = null;
            });
        }
        return promise;
    }
    function dispatch(node, direction, kind) {
        node.dispatchEvent(custom_event(`${direction ? 'intro' : 'outro'}${kind}`));
    }
    const outroing = new Set();
    let outros;
    function group_outros() {
        outros = {
            r: 0,
            c: [],
            p: outros // parent group
        };
    }
    function check_outros() {
        if (!outros.r) {
            run_all(outros.c);
        }
        outros = outros.p;
    }
    function transition_in(block, local) {
        if (block && block.i) {
            outroing.delete(block);
            block.i(local);
        }
    }
    function transition_out(block, local, detach, callback) {
        if (block && block.o) {
            if (outroing.has(block))
                return;
            outroing.add(block);
            outros.c.push(() => {
                outroing.delete(block);
                if (callback) {
                    if (detach)
                        block.d(1);
                    callback();
                }
            });
            block.o(local);
        }
    }
    const null_transition = { duration: 0 };
    function create_in_transition(node, fn, params) {
        let config = fn(node, params);
        let running = false;
        let animation_name;
        let task;
        let uid = 0;
        function cleanup() {
            if (animation_name)
                delete_rule(node, animation_name);
        }
        function go() {
            const { delay = 0, duration = 300, easing = identity, tick = noop, css } = config || null_transition;
            if (css)
                animation_name = create_rule(node, 0, 1, duration, delay, easing, css, uid++);
            tick(0, 1);
            const start_time = now() + delay;
            const end_time = start_time + duration;
            if (task)
                task.abort();
            running = true;
            add_render_callback(() => dispatch(node, true, 'start'));
            task = loop(now => {
                if (running) {
                    if (now >= end_time) {
                        tick(1, 0);
                        dispatch(node, true, 'end');
                        cleanup();
                        return running = false;
                    }
                    if (now >= start_time) {
                        const t = easing((now - start_time) / duration);
                        tick(t, 1 - t);
                    }
                }
                return running;
            });
        }
        let started = false;
        return {
            start() {
                if (started)
                    return;
                delete_rule(node);
                if (is_function(config)) {
                    config = config();
                    wait().then(go);
                }
                else {
                    go();
                }
            },
            invalidate() {
                started = false;
            },
            end() {
                if (running) {
                    cleanup();
                    running = false;
                }
            }
        };
    }
    function create_out_transition(node, fn, params) {
        let config = fn(node, params);
        let running = true;
        let animation_name;
        const group = outros;
        group.r += 1;
        function go() {
            const { delay = 0, duration = 300, easing = identity, tick = noop, css } = config || null_transition;
            if (css)
                animation_name = create_rule(node, 1, 0, duration, delay, easing, css);
            const start_time = now() + delay;
            const end_time = start_time + duration;
            add_render_callback(() => dispatch(node, false, 'start'));
            loop(now => {
                if (running) {
                    if (now >= end_time) {
                        tick(0, 1);
                        dispatch(node, false, 'end');
                        if (!--group.r) {
                            // this will result in `end()` being called,
                            // so we don't need to clean up here
                            run_all(group.c);
                        }
                        return false;
                    }
                    if (now >= start_time) {
                        const t = easing((now - start_time) / duration);
                        tick(1 - t, t);
                    }
                }
                return running;
            });
        }
        if (is_function(config)) {
            wait().then(() => {
                // @ts-ignore
                config = config();
                go();
            });
        }
        else {
            go();
        }
        return {
            end(reset) {
                if (reset && config.tick) {
                    config.tick(1, 0);
                }
                if (running) {
                    if (animation_name)
                        delete_rule(node, animation_name);
                    running = false;
                }
            }
        };
    }
    function create_component(block) {
        block && block.c();
    }
    function mount_component(component, target, anchor) {
        const { fragment, on_mount, on_destroy, after_update } = component.$$;
        fragment && fragment.m(target, anchor);
        // onMount happens before the initial afterUpdate
        add_render_callback(() => {
            const new_on_destroy = on_mount.map(run).filter(is_function);
            if (on_destroy) {
                on_destroy.push(...new_on_destroy);
            }
            else {
                // Edge case - component was destroyed immediately,
                // most likely as a result of a binding initialising
                run_all(new_on_destroy);
            }
            component.$$.on_mount = [];
        });
        after_update.forEach(add_render_callback);
    }
    function destroy_component(component, detaching) {
        const $$ = component.$$;
        if ($$.fragment !== null) {
            run_all($$.on_destroy);
            $$.fragment && $$.fragment.d(detaching);
            // TODO null out other refs, including component.$$ (but need to
            // preserve final state?)
            $$.on_destroy = $$.fragment = null;
            $$.ctx = [];
        }
    }
    function make_dirty(component, i) {
        if (component.$$.dirty[0] === -1) {
            dirty_components.push(component);
            schedule_update();
            component.$$.dirty.fill(0);
        }
        component.$$.dirty[(i / 31) | 0] |= (1 << (i % 31));
    }
    function init(component, options, instance, create_fragment, not_equal, props, dirty = [-1]) {
        const parent_component = current_component;
        set_current_component(component);
        const $$ = component.$$ = {
            fragment: null,
            ctx: null,
            // state
            props,
            update: noop,
            not_equal,
            bound: blank_object(),
            // lifecycle
            on_mount: [],
            on_destroy: [],
            before_update: [],
            after_update: [],
            context: new Map(parent_component ? parent_component.$$.context : []),
            // everything else
            callbacks: blank_object(),
            dirty,
            skip_bound: false
        };
        let ready = false;
        $$.ctx = instance
            ? instance(component, options.props || {}, (i, ret, ...rest) => {
                const value = rest.length ? rest[0] : ret;
                if ($$.ctx && not_equal($$.ctx[i], $$.ctx[i] = value)) {
                    if (!$$.skip_bound && $$.bound[i])
                        $$.bound[i](value);
                    if (ready)
                        make_dirty(component, i);
                }
                return ret;
            })
            : [];
        $$.update();
        ready = true;
        run_all($$.before_update);
        // `false` as a special case of no DOM component
        $$.fragment = create_fragment ? create_fragment($$.ctx) : false;
        if (options.target) {
            if (options.hydrate) {
                const nodes = children(options.target);
                // eslint-disable-next-line @typescript-eslint/no-non-null-assertion
                $$.fragment && $$.fragment.l(nodes);
                nodes.forEach(detach);
            }
            else {
                // eslint-disable-next-line @typescript-eslint/no-non-null-assertion
                $$.fragment && $$.fragment.c();
            }
            if (options.intro)
                transition_in(component.$$.fragment);
            mount_component(component, options.target, options.anchor);
            flush();
        }
        set_current_component(parent_component);
    }
    /**
     * Base class for Svelte components. Used when dev=false.
     */
    class SvelteComponent {
        $destroy() {
            destroy_component(this, 1);
            this.$destroy = noop;
        }
        $on(type, callback) {
            const callbacks = (this.$$.callbacks[type] || (this.$$.callbacks[type] = []));
            callbacks.push(callback);
            return () => {
                const index = callbacks.indexOf(callback);
                if (index !== -1)
                    callbacks.splice(index, 1);
            };
        }
        $set($$props) {
            if (this.$$set && !is_empty($$props)) {
                this.$$.skip_bound = true;
                this.$$set($$props);
                this.$$.skip_bound = false;
            }
        }
    }

    function dispatch_dev(type, detail) {
        document.dispatchEvent(custom_event(type, Object.assign({ version: '3.32.3' }, detail)));
    }
    function append_dev(target, node) {
        dispatch_dev('SvelteDOMInsert', { target, node });
        append(target, node);
    }
    function insert_dev(target, node, anchor) {
        dispatch_dev('SvelteDOMInsert', { target, node, anchor });
        insert(target, node, anchor);
    }
    function detach_dev(node) {
        dispatch_dev('SvelteDOMRemove', { node });
        detach(node);
    }
    function listen_dev(node, event, handler, options, has_prevent_default, has_stop_propagation) {
        const modifiers = options === true ? ['capture'] : options ? Array.from(Object.keys(options)) : [];
        if (has_prevent_default)
            modifiers.push('preventDefault');
        if (has_stop_propagation)
            modifiers.push('stopPropagation');
        dispatch_dev('SvelteDOMAddEventListener', { node, event, handler, modifiers });
        const dispose = listen(node, event, handler, options);
        return () => {
            dispatch_dev('SvelteDOMRemoveEventListener', { node, event, handler, modifiers });
            dispose();
        };
    }
    function attr_dev(node, attribute, value) {
        attr(node, attribute, value);
        if (value == null)
            dispatch_dev('SvelteDOMRemoveAttribute', { node, attribute });
        else
            dispatch_dev('SvelteDOMSetAttribute', { node, attribute, value });
    }
    function prop_dev(node, property, value) {
        node[property] = value;
        dispatch_dev('SvelteDOMSetProperty', { node, property, value });
    }
    function set_data_dev(text, data) {
        data = '' + data;
        if (text.wholeText === data)
            return;
        dispatch_dev('SvelteDOMSetData', { node: text, data });
        text.data = data;
    }
    function validate_each_argument(arg) {
        if (typeof arg !== 'string' && !(arg && typeof arg === 'object' && 'length' in arg)) {
            let msg = '{#each} only iterates over array-like objects.';
            if (typeof Symbol === 'function' && arg && Symbol.iterator in arg) {
                msg += ' You can use a spread to convert this iterable into an array.';
            }
            throw new Error(msg);
        }
    }
    function validate_slots(name, slot, keys) {
        for (const slot_key of Object.keys(slot)) {
            if (!~keys.indexOf(slot_key)) {
                console.warn(`<${name}> received an unexpected slot "${slot_key}".`);
            }
        }
    }
    /**
     * Base class for Svelte components with some minor dev-enhancements. Used when dev=true.
     */
    class SvelteComponentDev extends SvelteComponent {
        constructor(options) {
            if (!options || (!options.target && !options.$$inline)) {
                throw new Error("'target' is a required option");
            }
            super();
        }
        $destroy() {
            super.$destroy();
            this.$destroy = () => {
                console.warn('Component was already destroyed'); // eslint-disable-line no-console
            };
        }
        $capture_state() { }
        $inject_state() { }
    }

    function cubicOut(t) {
        const f = t - 1.0;
        return f * f * f + 1.0;
    }

    function fade(node, { delay = 0, duration = 400, easing = identity } = {}) {
        const o = +getComputedStyle(node).opacity;
        return {
            delay,
            duration,
            easing,
            css: t => `opacity: ${t * o}`
        };
    }
    function fly(node, { delay = 0, duration = 400, easing = cubicOut, x = 0, y = 0, opacity = 0 } = {}) {
        const style = getComputedStyle(node);
        const target_opacity = +style.opacity;
        const transform = style.transform === 'none' ? '' : style.transform;
        const od = target_opacity * (1 - opacity);
        return {
            delay,
            duration,
            easing,
            css: (t, u) => `
			transform: ${transform} translate(${(1 - t) * x}px, ${(1 - t) * y}px);
			opacity: ${target_opacity - (od * u)}`
        };
    }

    /**
     * Validation methods
     *
     * @since 1.0.0
     */
    class ValidationMedhods {
        /**
         * Is value of tye number?
         *
         * @param value Value which have to be checked.
         * @return boolean True if is of type number, false if not.
         *
         * @since 1.0.0
         */
        static number(value) {
            if (typeof value === 'number' || value instanceof Number) {
                return true;
            }
            return false;
        }
        /**
         * Is value of tye string?
         *
         * @param value Value which have to be checked.
         * @return boolean True if is of type string, false if not.
         *
         * @since 1.0.0
         */
        static string(value) {
            if (typeof value === 'string' || value instanceof String) {
                return true;
            }
            return false;
        }
        /**
         * Is value of tye email?
         *
         * @param value Value which have to be checked.
         * @return boolean True if is of type email, false if not.
         *
         * @since 1.0.0
         */
        static email(value) {
            const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(value).toLowerCase());
        }
        /**
         * Is number not under min?
         *
         * @param value Value which have to be checked.
         * @param min Max number.
         *
         * @return boolean True if number not under min.
         *
         * @since 1.0.0
         */
        static min(value, min) {
            return !(value < min);
        }
        /**
         * Is number not over max?
         *
         * @param value Value which have to be checked.
         * @param min Min number.
         *
         * @return boolean True if number not over max.
         *
         * @since 1.0.0
         */
        static max(value, max) {
            return !(value > max);
        }
        /**
         * Is string not under min length?
         *
         * @param value Value which have to be checked.
         * @param min Max number of chars.
         *
         * @return boolean True if string length is not under min length.
         *
         * @since 1.0.0
         */
        static minLength(value, min) {
            return !(value.length < min);
        }
        /**
         * Is string not over max length?
         *
         * @param value Value which have to be checked.
         * @param min Max number of chars.
         *
         * @return boolean True if string length is not over max length.
         *
         * @since 1.0.0
         */
        static maxLength(value, max) {
            return !(value.length > max);
        }
        /**
         * Is value empty?
         *
         * @param value Value which have to be checked.
         * @return boolean True if is empty, false if not.
         *
         * @since 1.0.0
         */
        static empty(value) {
            if (value === undefined || value.trim() === '') {
                return true;
            }
            return false;
        }
        static inArray(value, values = []) {
            return values.includes(value);
        }
    }

    /**
     * Validator class.
     *
     * @since 1.0.0
     */
    class Validation {
        /**
         * Constructor.
         *
         * @param value      A value which have to be validated.
         * @param validation Validation rules array.
         * @param required   Is value required or not.
         *
         * @since 1.0.0
         */
        constructor(value, validations, required) {
            this.value = value;
            this.validations = validations;
            this.required = required;
        }
        /**
         * Doing check for given values.
         *
         * @since 1.0.0
         */
        check() {
            let errors = [];
            if (this.required && this.value === undefined) {
                errors.push('Das Feld darf nicht leer bleiben');
                return errors;
            }
            // Running each validation
            this.validations.forEach(validation => {
                // Assigning Validation functions
                switch (validation.type) {
                    case 'string':
                        if (!ValidationMedhods.string(this.value)) {
                            errors.push(validation.error);
                        }
                        break;
                    case 'number':
                        if (!ValidationMedhods.number(this.value)) {
                            errors.push(validation.error);
                        }
                        break;
                    case 'email':
                        if (!ValidationMedhods.email(this.value)) {
                            errors.push(validation.error);
                        }
                        break;
                    case 'min':
                        if (!ValidationMedhods.min(this.value, validation.value)) {
                            errors.push(validation.error);
                        }
                        break;
                    case 'max':
                        if (!ValidationMedhods.max(this.value, validation.value)) {
                            errors.push(validation.error);
                        }
                        break;
                    case 'minLength':
                        if (!ValidationMedhods.minLength(this.value, validation.value)) {
                            errors.push(validation.error);
                        }
                        break;
                    case 'maxLength':
                        if (!ValidationMedhods.maxLength(this.value, validation.value)) {
                            errors.push(validation.error);
                        }
                        break;
                    case 'inArray':
                        if (!ValidationMedhods.inArray(this.value, validation.values)) {
                            errors.push(validation.error);
                        }
                        break;
                    default:
                        errors.push('Validations-Typ "' + validation.type + '" existiert nicht."');
                        break;
                }
            });
            return errors;
        }
    }

    /**
     * Field class.
     *
     * @since 1.0.0
     */
    class Field {
        /**
         * Initializing field.
         *
         * @param name  Name of the field.
         * @param field Field object
         *
         * @since 1.0.0
         */
        constructor(fieldset, field) {
            this.errors = [];
            this.fieldset = fieldset;
            this.name = field.name;
            this.type = field.type;
            this.label = field.label;
            this.choices = field.choices === undefined ? [] : field.choices;
            this.params = field.params === undefined ? [] : field.params;
            this.classes = field.classes === undefined ? [] : field.classes;
            this.required = field.required === undefined ? false : true;
            this.validations = field.validations === undefined ? [] : field.validations;
            this.value = field.value;
        }
        /**
         * Set value of field.
         *
         * @param value Value to set.
         *
         * @since 1.0.0
         */
        setValue(value) {
            this.value = value;
        }
        /**
         * Get value of field.
         *
         * @param value Value to set.
         *
         * @since 1.0.0
         */
        getValue() {
            return this.value;
        }
        /**
         * Does field have choices.
         *
         * @return True if it has choices, false if not.
         *
         * @since 1.0.0
         */
        hasChoices() {
            return this.choices.length > 0;
        }
        /**
         * Add a CSS class to field.
         *
         * @param className CSS class name.
         *
         * @since 1.0.0
         */
        addClass(className) {
            this.classes.push(className);
        }
        /**
         * Get CSS Classes.
         *
         * @return String of CSS classes.
         *
         * @since 1.0.0
         */
        getClasses() {
            if (this.classes.length > 0) {
                return this.classes.join(' ');
            }
            return '';
        }
        /**
         * Validate the field.
         *
         * @return Array of errors, empty array on no errors.
         *
         * @since 1.0.0
         */
        validate() {
            let validation = new Validation(this.value, this.validations, this.required);
            this.errors = validation.check();
            if (this.errors.length > 0) {
                this.addClass('error');
            }
            else {
                this.classes.forEach((value, i) => {
                    if (value === 'error')
                        this.classes.splice(i, 1);
                });
            }
            return this.errors;
        }
        /**
         * Get validation errors.
         *
         * @return Erros which occured while validating.
         *
         * @since 1.0.0
         */
        getValidationErors() {
            return this.errors;
        }
        /**
         * Has field validation errors.
         *
         * @return True if field has errors, false if not.
         *
         * @since 1.0.0
         */
        hasValidationErrors() {
            if (this.errors.length > 0) {
                return true;
            }
            return false;
        }
    }

    /**
     * Class Fieldset.
     *
     * @since 1.0.0
     */
    class Fieldset {
        /**
         * Initializing fieldset.
         *
         * @param form Form object.
         * @param fieldset Fieldset data.
         *
         * @since 1.0.0
         */
        constructor(form, fieldset) {
            this.fields = [];
            this.form = form;
            this.name = fieldset.name;
            this.label = fieldset.label;
            this.percentage = fieldset.percentage;
            this.params = undefined === fieldset.params ? [] : fieldset.params;
            this.classes = undefined === fieldset.classes ? [] : fieldset.classes;
            this.fieldsClasses = undefined === fieldset.fieldsClasses ? [] : fieldset.fieldsClasses;
            this.conditions = fieldset.conditions;
            this.submission = fieldset.submission;
            this.nextFieldset = fieldset.nextFieldset;
            this.prevFieldset = fieldset.prevFieldset;
            fieldset.fields.forEach(field => {
                this.fields.push(new Field(this, field));
            });
        }
        conditionsFullfilled() {
            if (this.conditions === undefined) {
                return true;
            }
            let fullfillments = [];
            this.conditions.forEach((condition) => {
                let fullfilled = false;
                let field = this.form.getField(condition.field);
                switch (condition.operator) {
                    case '==':
                        fullfilled = condition.value === field.getValue();
                        break;
                    case '!=':
                        fullfilled = condition.value !== field.getValue();
                        break;
                    case '>':
                        fullfilled = condition.value !== field.getValue();
                        break;
                    case '<':
                        fullfilled = condition.value !== field.getValue();
                        break;
                    default:
                        throw new Error('Operator "' + condition.operator + '" does not exist.');
                }
                fullfillments.push(fullfilled);
            });
            return !fullfillments.includes(false);
        }
        /**
         * Is there a submission to do?
         *
         * @return True if there is a submissionto do, false if not.
         *
         * @since 1.0.0
         */
        hasSubmission() {
            return this.submission !== undefined;
        }
        /**
         * Get CSS classes.
         *
         * @return String of CSS classes.
         *
         * @since 1.0.0
         */
        getClasses() {
            if (this.classes.length > 0) {
                return this.classes.join(' ');
            }
            return '';
        }
        /**
         * Get CSS field classes.
         *
         * @return String of CSS classes.
         *
         * @since 1.0.0
         */
        getFieldsClasses() {
            if (this.fieldsClasses.length > 0) {
                return this.fieldsClasses.join(' ');
            }
            return '';
        }
        /**
         * Validate fieldset.
         *
         * @return True on successful validation, false on errors.
         *
         * @since 1.0.0
         */
        validate() {
            let foundError = false;
            this.fields.forEach((field, i) => {
                if (field.validate().length > 0 && !foundError) {
                    foundError = true;
                }
            });
            return foundError;
        }
        /**
         * Has fieldset validation errors.
         *
         * @return True if fieldset has errors, false if not.
         *
         * @since 1.0.0
         */
        hasValidationErrors() {
            let foundError = false;
            this.fields.forEach((field) => {
                if (field.hasValidationErrors() && !foundError) {
                    foundError = true;
                }
            });
            return foundError;
        }
    }

    var bind = function bind(fn, thisArg) {
      return function wrap() {
        var args = new Array(arguments.length);
        for (var i = 0; i < args.length; i++) {
          args[i] = arguments[i];
        }
        return fn.apply(thisArg, args);
      };
    };

    /*global toString:true*/

    // utils is a library of generic helper functions non-specific to axios

    var toString = Object.prototype.toString;

    /**
     * Determine if a value is an Array
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is an Array, otherwise false
     */
    function isArray(val) {
      return toString.call(val) === '[object Array]';
    }

    /**
     * Determine if a value is undefined
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if the value is undefined, otherwise false
     */
    function isUndefined(val) {
      return typeof val === 'undefined';
    }

    /**
     * Determine if a value is a Buffer
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is a Buffer, otherwise false
     */
    function isBuffer(val) {
      return val !== null && !isUndefined(val) && val.constructor !== null && !isUndefined(val.constructor)
        && typeof val.constructor.isBuffer === 'function' && val.constructor.isBuffer(val);
    }

    /**
     * Determine if a value is an ArrayBuffer
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is an ArrayBuffer, otherwise false
     */
    function isArrayBuffer(val) {
      return toString.call(val) === '[object ArrayBuffer]';
    }

    /**
     * Determine if a value is a FormData
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is an FormData, otherwise false
     */
    function isFormData(val) {
      return (typeof FormData !== 'undefined') && (val instanceof FormData);
    }

    /**
     * Determine if a value is a view on an ArrayBuffer
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is a view on an ArrayBuffer, otherwise false
     */
    function isArrayBufferView(val) {
      var result;
      if ((typeof ArrayBuffer !== 'undefined') && (ArrayBuffer.isView)) {
        result = ArrayBuffer.isView(val);
      } else {
        result = (val) && (val.buffer) && (val.buffer instanceof ArrayBuffer);
      }
      return result;
    }

    /**
     * Determine if a value is a String
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is a String, otherwise false
     */
    function isString(val) {
      return typeof val === 'string';
    }

    /**
     * Determine if a value is a Number
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is a Number, otherwise false
     */
    function isNumber(val) {
      return typeof val === 'number';
    }

    /**
     * Determine if a value is an Object
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is an Object, otherwise false
     */
    function isObject(val) {
      return val !== null && typeof val === 'object';
    }

    /**
     * Determine if a value is a plain Object
     *
     * @param {Object} val The value to test
     * @return {boolean} True if value is a plain Object, otherwise false
     */
    function isPlainObject(val) {
      if (toString.call(val) !== '[object Object]') {
        return false;
      }

      var prototype = Object.getPrototypeOf(val);
      return prototype === null || prototype === Object.prototype;
    }

    /**
     * Determine if a value is a Date
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is a Date, otherwise false
     */
    function isDate(val) {
      return toString.call(val) === '[object Date]';
    }

    /**
     * Determine if a value is a File
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is a File, otherwise false
     */
    function isFile(val) {
      return toString.call(val) === '[object File]';
    }

    /**
     * Determine if a value is a Blob
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is a Blob, otherwise false
     */
    function isBlob(val) {
      return toString.call(val) === '[object Blob]';
    }

    /**
     * Determine if a value is a Function
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is a Function, otherwise false
     */
    function isFunction(val) {
      return toString.call(val) === '[object Function]';
    }

    /**
     * Determine if a value is a Stream
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is a Stream, otherwise false
     */
    function isStream(val) {
      return isObject(val) && isFunction(val.pipe);
    }

    /**
     * Determine if a value is a URLSearchParams object
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is a URLSearchParams object, otherwise false
     */
    function isURLSearchParams(val) {
      return typeof URLSearchParams !== 'undefined' && val instanceof URLSearchParams;
    }

    /**
     * Trim excess whitespace off the beginning and end of a string
     *
     * @param {String} str The String to trim
     * @returns {String} The String freed of excess whitespace
     */
    function trim(str) {
      return str.replace(/^\s*/, '').replace(/\s*$/, '');
    }

    /**
     * Determine if we're running in a standard browser environment
     *
     * This allows axios to run in a web worker, and react-native.
     * Both environments support XMLHttpRequest, but not fully standard globals.
     *
     * web workers:
     *  typeof window -> undefined
     *  typeof document -> undefined
     *
     * react-native:
     *  navigator.product -> 'ReactNative'
     * nativescript
     *  navigator.product -> 'NativeScript' or 'NS'
     */
    function isStandardBrowserEnv() {
      if (typeof navigator !== 'undefined' && (navigator.product === 'ReactNative' ||
                                               navigator.product === 'NativeScript' ||
                                               navigator.product === 'NS')) {
        return false;
      }
      return (
        typeof window !== 'undefined' &&
        typeof document !== 'undefined'
      );
    }

    /**
     * Iterate over an Array or an Object invoking a function for each item.
     *
     * If `obj` is an Array callback will be called passing
     * the value, index, and complete array for each item.
     *
     * If 'obj' is an Object callback will be called passing
     * the value, key, and complete object for each property.
     *
     * @param {Object|Array} obj The object to iterate
     * @param {Function} fn The callback to invoke for each item
     */
    function forEach(obj, fn) {
      // Don't bother if no value provided
      if (obj === null || typeof obj === 'undefined') {
        return;
      }

      // Force an array if not already something iterable
      if (typeof obj !== 'object') {
        /*eslint no-param-reassign:0*/
        obj = [obj];
      }

      if (isArray(obj)) {
        // Iterate over array values
        for (var i = 0, l = obj.length; i < l; i++) {
          fn.call(null, obj[i], i, obj);
        }
      } else {
        // Iterate over object keys
        for (var key in obj) {
          if (Object.prototype.hasOwnProperty.call(obj, key)) {
            fn.call(null, obj[key], key, obj);
          }
        }
      }
    }

    /**
     * Accepts varargs expecting each argument to be an object, then
     * immutably merges the properties of each object and returns result.
     *
     * When multiple objects contain the same key the later object in
     * the arguments list will take precedence.
     *
     * Example:
     *
     * ```js
     * var result = merge({foo: 123}, {foo: 456});
     * console.log(result.foo); // outputs 456
     * ```
     *
     * @param {Object} obj1 Object to merge
     * @returns {Object} Result of all merge properties
     */
    function merge(/* obj1, obj2, obj3, ... */) {
      var result = {};
      function assignValue(val, key) {
        if (isPlainObject(result[key]) && isPlainObject(val)) {
          result[key] = merge(result[key], val);
        } else if (isPlainObject(val)) {
          result[key] = merge({}, val);
        } else if (isArray(val)) {
          result[key] = val.slice();
        } else {
          result[key] = val;
        }
      }

      for (var i = 0, l = arguments.length; i < l; i++) {
        forEach(arguments[i], assignValue);
      }
      return result;
    }

    /**
     * Extends object a by mutably adding to it the properties of object b.
     *
     * @param {Object} a The object to be extended
     * @param {Object} b The object to copy properties from
     * @param {Object} thisArg The object to bind function to
     * @return {Object} The resulting value of object a
     */
    function extend(a, b, thisArg) {
      forEach(b, function assignValue(val, key) {
        if (thisArg && typeof val === 'function') {
          a[key] = bind(val, thisArg);
        } else {
          a[key] = val;
        }
      });
      return a;
    }

    /**
     * Remove byte order marker. This catches EF BB BF (the UTF-8 BOM)
     *
     * @param {string} content with BOM
     * @return {string} content value without BOM
     */
    function stripBOM(content) {
      if (content.charCodeAt(0) === 0xFEFF) {
        content = content.slice(1);
      }
      return content;
    }

    var utils = {
      isArray: isArray,
      isArrayBuffer: isArrayBuffer,
      isBuffer: isBuffer,
      isFormData: isFormData,
      isArrayBufferView: isArrayBufferView,
      isString: isString,
      isNumber: isNumber,
      isObject: isObject,
      isPlainObject: isPlainObject,
      isUndefined: isUndefined,
      isDate: isDate,
      isFile: isFile,
      isBlob: isBlob,
      isFunction: isFunction,
      isStream: isStream,
      isURLSearchParams: isURLSearchParams,
      isStandardBrowserEnv: isStandardBrowserEnv,
      forEach: forEach,
      merge: merge,
      extend: extend,
      trim: trim,
      stripBOM: stripBOM
    };

    function encode(val) {
      return encodeURIComponent(val).
        replace(/%3A/gi, ':').
        replace(/%24/g, '$').
        replace(/%2C/gi, ',').
        replace(/%20/g, '+').
        replace(/%5B/gi, '[').
        replace(/%5D/gi, ']');
    }

    /**
     * Build a URL by appending params to the end
     *
     * @param {string} url The base of the url (e.g., http://www.google.com)
     * @param {object} [params] The params to be appended
     * @returns {string} The formatted url
     */
    var buildURL = function buildURL(url, params, paramsSerializer) {
      /*eslint no-param-reassign:0*/
      if (!params) {
        return url;
      }

      var serializedParams;
      if (paramsSerializer) {
        serializedParams = paramsSerializer(params);
      } else if (utils.isURLSearchParams(params)) {
        serializedParams = params.toString();
      } else {
        var parts = [];

        utils.forEach(params, function serialize(val, key) {
          if (val === null || typeof val === 'undefined') {
            return;
          }

          if (utils.isArray(val)) {
            key = key + '[]';
          } else {
            val = [val];
          }

          utils.forEach(val, function parseValue(v) {
            if (utils.isDate(v)) {
              v = v.toISOString();
            } else if (utils.isObject(v)) {
              v = JSON.stringify(v);
            }
            parts.push(encode(key) + '=' + encode(v));
          });
        });

        serializedParams = parts.join('&');
      }

      if (serializedParams) {
        var hashmarkIndex = url.indexOf('#');
        if (hashmarkIndex !== -1) {
          url = url.slice(0, hashmarkIndex);
        }

        url += (url.indexOf('?') === -1 ? '?' : '&') + serializedParams;
      }

      return url;
    };

    function InterceptorManager() {
      this.handlers = [];
    }

    /**
     * Add a new interceptor to the stack
     *
     * @param {Function} fulfilled The function to handle `then` for a `Promise`
     * @param {Function} rejected The function to handle `reject` for a `Promise`
     *
     * @return {Number} An ID used to remove interceptor later
     */
    InterceptorManager.prototype.use = function use(fulfilled, rejected) {
      this.handlers.push({
        fulfilled: fulfilled,
        rejected: rejected
      });
      return this.handlers.length - 1;
    };

    /**
     * Remove an interceptor from the stack
     *
     * @param {Number} id The ID that was returned by `use`
     */
    InterceptorManager.prototype.eject = function eject(id) {
      if (this.handlers[id]) {
        this.handlers[id] = null;
      }
    };

    /**
     * Iterate over all the registered interceptors
     *
     * This method is particularly useful for skipping over any
     * interceptors that may have become `null` calling `eject`.
     *
     * @param {Function} fn The function to call for each interceptor
     */
    InterceptorManager.prototype.forEach = function forEach(fn) {
      utils.forEach(this.handlers, function forEachHandler(h) {
        if (h !== null) {
          fn(h);
        }
      });
    };

    var InterceptorManager_1 = InterceptorManager;

    /**
     * Transform the data for a request or a response
     *
     * @param {Object|String} data The data to be transformed
     * @param {Array} headers The headers for the request or response
     * @param {Array|Function} fns A single function or Array of functions
     * @returns {*} The resulting transformed data
     */
    var transformData = function transformData(data, headers, fns) {
      /*eslint no-param-reassign:0*/
      utils.forEach(fns, function transform(fn) {
        data = fn(data, headers);
      });

      return data;
    };

    var isCancel = function isCancel(value) {
      return !!(value && value.__CANCEL__);
    };

    var normalizeHeaderName = function normalizeHeaderName(headers, normalizedName) {
      utils.forEach(headers, function processHeader(value, name) {
        if (name !== normalizedName && name.toUpperCase() === normalizedName.toUpperCase()) {
          headers[normalizedName] = value;
          delete headers[name];
        }
      });
    };

    /**
     * Update an Error with the specified config, error code, and response.
     *
     * @param {Error} error The error to update.
     * @param {Object} config The config.
     * @param {string} [code] The error code (for example, 'ECONNABORTED').
     * @param {Object} [request] The request.
     * @param {Object} [response] The response.
     * @returns {Error} The error.
     */
    var enhanceError = function enhanceError(error, config, code, request, response) {
      error.config = config;
      if (code) {
        error.code = code;
      }

      error.request = request;
      error.response = response;
      error.isAxiosError = true;

      error.toJSON = function toJSON() {
        return {
          // Standard
          message: this.message,
          name: this.name,
          // Microsoft
          description: this.description,
          number: this.number,
          // Mozilla
          fileName: this.fileName,
          lineNumber: this.lineNumber,
          columnNumber: this.columnNumber,
          stack: this.stack,
          // Axios
          config: this.config,
          code: this.code
        };
      };
      return error;
    };

    /**
     * Create an Error with the specified message, config, error code, request and response.
     *
     * @param {string} message The error message.
     * @param {Object} config The config.
     * @param {string} [code] The error code (for example, 'ECONNABORTED').
     * @param {Object} [request] The request.
     * @param {Object} [response] The response.
     * @returns {Error} The created error.
     */
    var createError = function createError(message, config, code, request, response) {
      var error = new Error(message);
      return enhanceError(error, config, code, request, response);
    };

    /**
     * Resolve or reject a Promise based on response status.
     *
     * @param {Function} resolve A function that resolves the promise.
     * @param {Function} reject A function that rejects the promise.
     * @param {object} response The response.
     */
    var settle = function settle(resolve, reject, response) {
      var validateStatus = response.config.validateStatus;
      if (!response.status || !validateStatus || validateStatus(response.status)) {
        resolve(response);
      } else {
        reject(createError(
          'Request failed with status code ' + response.status,
          response.config,
          null,
          response.request,
          response
        ));
      }
    };

    var cookies = (
      utils.isStandardBrowserEnv() ?

      // Standard browser envs support document.cookie
        (function standardBrowserEnv() {
          return {
            write: function write(name, value, expires, path, domain, secure) {
              var cookie = [];
              cookie.push(name + '=' + encodeURIComponent(value));

              if (utils.isNumber(expires)) {
                cookie.push('expires=' + new Date(expires).toGMTString());
              }

              if (utils.isString(path)) {
                cookie.push('path=' + path);
              }

              if (utils.isString(domain)) {
                cookie.push('domain=' + domain);
              }

              if (secure === true) {
                cookie.push('secure');
              }

              document.cookie = cookie.join('; ');
            },

            read: function read(name) {
              var match = document.cookie.match(new RegExp('(^|;\\s*)(' + name + ')=([^;]*)'));
              return (match ? decodeURIComponent(match[3]) : null);
            },

            remove: function remove(name) {
              this.write(name, '', Date.now() - 86400000);
            }
          };
        })() :

      // Non standard browser env (web workers, react-native) lack needed support.
        (function nonStandardBrowserEnv() {
          return {
            write: function write() {},
            read: function read() { return null; },
            remove: function remove() {}
          };
        })()
    );

    /**
     * Determines whether the specified URL is absolute
     *
     * @param {string} url The URL to test
     * @returns {boolean} True if the specified URL is absolute, otherwise false
     */
    var isAbsoluteURL = function isAbsoluteURL(url) {
      // A URL is considered absolute if it begins with "<scheme>://" or "//" (protocol-relative URL).
      // RFC 3986 defines scheme name as a sequence of characters beginning with a letter and followed
      // by any combination of letters, digits, plus, period, or hyphen.
      return /^([a-z][a-z\d\+\-\.]*:)?\/\//i.test(url);
    };

    /**
     * Creates a new URL by combining the specified URLs
     *
     * @param {string} baseURL The base URL
     * @param {string} relativeURL The relative URL
     * @returns {string} The combined URL
     */
    var combineURLs = function combineURLs(baseURL, relativeURL) {
      return relativeURL
        ? baseURL.replace(/\/+$/, '') + '/' + relativeURL.replace(/^\/+/, '')
        : baseURL;
    };

    /**
     * Creates a new URL by combining the baseURL with the requestedURL,
     * only when the requestedURL is not already an absolute URL.
     * If the requestURL is absolute, this function returns the requestedURL untouched.
     *
     * @param {string} baseURL The base URL
     * @param {string} requestedURL Absolute or relative URL to combine
     * @returns {string} The combined full path
     */
    var buildFullPath = function buildFullPath(baseURL, requestedURL) {
      if (baseURL && !isAbsoluteURL(requestedURL)) {
        return combineURLs(baseURL, requestedURL);
      }
      return requestedURL;
    };

    // Headers whose duplicates are ignored by node
    // c.f. https://nodejs.org/api/http.html#http_message_headers
    var ignoreDuplicateOf = [
      'age', 'authorization', 'content-length', 'content-type', 'etag',
      'expires', 'from', 'host', 'if-modified-since', 'if-unmodified-since',
      'last-modified', 'location', 'max-forwards', 'proxy-authorization',
      'referer', 'retry-after', 'user-agent'
    ];

    /**
     * Parse headers into an object
     *
     * ```
     * Date: Wed, 27 Aug 2014 08:58:49 GMT
     * Content-Type: application/json
     * Connection: keep-alive
     * Transfer-Encoding: chunked
     * ```
     *
     * @param {String} headers Headers needing to be parsed
     * @returns {Object} Headers parsed into an object
     */
    var parseHeaders = function parseHeaders(headers) {
      var parsed = {};
      var key;
      var val;
      var i;

      if (!headers) { return parsed; }

      utils.forEach(headers.split('\n'), function parser(line) {
        i = line.indexOf(':');
        key = utils.trim(line.substr(0, i)).toLowerCase();
        val = utils.trim(line.substr(i + 1));

        if (key) {
          if (parsed[key] && ignoreDuplicateOf.indexOf(key) >= 0) {
            return;
          }
          if (key === 'set-cookie') {
            parsed[key] = (parsed[key] ? parsed[key] : []).concat([val]);
          } else {
            parsed[key] = parsed[key] ? parsed[key] + ', ' + val : val;
          }
        }
      });

      return parsed;
    };

    var isURLSameOrigin = (
      utils.isStandardBrowserEnv() ?

      // Standard browser envs have full support of the APIs needed to test
      // whether the request URL is of the same origin as current location.
        (function standardBrowserEnv() {
          var msie = /(msie|trident)/i.test(navigator.userAgent);
          var urlParsingNode = document.createElement('a');
          var originURL;

          /**
        * Parse a URL to discover it's components
        *
        * @param {String} url The URL to be parsed
        * @returns {Object}
        */
          function resolveURL(url) {
            var href = url;

            if (msie) {
            // IE needs attribute set twice to normalize properties
              urlParsingNode.setAttribute('href', href);
              href = urlParsingNode.href;
            }

            urlParsingNode.setAttribute('href', href);

            // urlParsingNode provides the UrlUtils interface - http://url.spec.whatwg.org/#urlutils
            return {
              href: urlParsingNode.href,
              protocol: urlParsingNode.protocol ? urlParsingNode.protocol.replace(/:$/, '') : '',
              host: urlParsingNode.host,
              search: urlParsingNode.search ? urlParsingNode.search.replace(/^\?/, '') : '',
              hash: urlParsingNode.hash ? urlParsingNode.hash.replace(/^#/, '') : '',
              hostname: urlParsingNode.hostname,
              port: urlParsingNode.port,
              pathname: (urlParsingNode.pathname.charAt(0) === '/') ?
                urlParsingNode.pathname :
                '/' + urlParsingNode.pathname
            };
          }

          originURL = resolveURL(window.location.href);

          /**
        * Determine if a URL shares the same origin as the current location
        *
        * @param {String} requestURL The URL to test
        * @returns {boolean} True if URL shares the same origin, otherwise false
        */
          return function isURLSameOrigin(requestURL) {
            var parsed = (utils.isString(requestURL)) ? resolveURL(requestURL) : requestURL;
            return (parsed.protocol === originURL.protocol &&
                parsed.host === originURL.host);
          };
        })() :

      // Non standard browser envs (web workers, react-native) lack needed support.
        (function nonStandardBrowserEnv() {
          return function isURLSameOrigin() {
            return true;
          };
        })()
    );

    var xhr = function xhrAdapter(config) {
      return new Promise(function dispatchXhrRequest(resolve, reject) {
        var requestData = config.data;
        var requestHeaders = config.headers;

        if (utils.isFormData(requestData)) {
          delete requestHeaders['Content-Type']; // Let the browser set it
        }

        var request = new XMLHttpRequest();

        // HTTP basic authentication
        if (config.auth) {
          var username = config.auth.username || '';
          var password = config.auth.password ? unescape(encodeURIComponent(config.auth.password)) : '';
          requestHeaders.Authorization = 'Basic ' + btoa(username + ':' + password);
        }

        var fullPath = buildFullPath(config.baseURL, config.url);
        request.open(config.method.toUpperCase(), buildURL(fullPath, config.params, config.paramsSerializer), true);

        // Set the request timeout in MS
        request.timeout = config.timeout;

        // Listen for ready state
        request.onreadystatechange = function handleLoad() {
          if (!request || request.readyState !== 4) {
            return;
          }

          // The request errored out and we didn't get a response, this will be
          // handled by onerror instead
          // With one exception: request that using file: protocol, most browsers
          // will return status as 0 even though it's a successful request
          if (request.status === 0 && !(request.responseURL && request.responseURL.indexOf('file:') === 0)) {
            return;
          }

          // Prepare the response
          var responseHeaders = 'getAllResponseHeaders' in request ? parseHeaders(request.getAllResponseHeaders()) : null;
          var responseData = !config.responseType || config.responseType === 'text' ? request.responseText : request.response;
          var response = {
            data: responseData,
            status: request.status,
            statusText: request.statusText,
            headers: responseHeaders,
            config: config,
            request: request
          };

          settle(resolve, reject, response);

          // Clean up request
          request = null;
        };

        // Handle browser request cancellation (as opposed to a manual cancellation)
        request.onabort = function handleAbort() {
          if (!request) {
            return;
          }

          reject(createError('Request aborted', config, 'ECONNABORTED', request));

          // Clean up request
          request = null;
        };

        // Handle low level network errors
        request.onerror = function handleError() {
          // Real errors are hidden from us by the browser
          // onerror should only fire if it's a network error
          reject(createError('Network Error', config, null, request));

          // Clean up request
          request = null;
        };

        // Handle timeout
        request.ontimeout = function handleTimeout() {
          var timeoutErrorMessage = 'timeout of ' + config.timeout + 'ms exceeded';
          if (config.timeoutErrorMessage) {
            timeoutErrorMessage = config.timeoutErrorMessage;
          }
          reject(createError(timeoutErrorMessage, config, 'ECONNABORTED',
            request));

          // Clean up request
          request = null;
        };

        // Add xsrf header
        // This is only done if running in a standard browser environment.
        // Specifically not if we're in a web worker, or react-native.
        if (utils.isStandardBrowserEnv()) {
          // Add xsrf header
          var xsrfValue = (config.withCredentials || isURLSameOrigin(fullPath)) && config.xsrfCookieName ?
            cookies.read(config.xsrfCookieName) :
            undefined;

          if (xsrfValue) {
            requestHeaders[config.xsrfHeaderName] = xsrfValue;
          }
        }

        // Add headers to the request
        if ('setRequestHeader' in request) {
          utils.forEach(requestHeaders, function setRequestHeader(val, key) {
            if (typeof requestData === 'undefined' && key.toLowerCase() === 'content-type') {
              // Remove Content-Type if data is undefined
              delete requestHeaders[key];
            } else {
              // Otherwise add header to the request
              request.setRequestHeader(key, val);
            }
          });
        }

        // Add withCredentials to request if needed
        if (!utils.isUndefined(config.withCredentials)) {
          request.withCredentials = !!config.withCredentials;
        }

        // Add responseType to request if needed
        if (config.responseType) {
          try {
            request.responseType = config.responseType;
          } catch (e) {
            // Expected DOMException thrown by browsers not compatible XMLHttpRequest Level 2.
            // But, this can be suppressed for 'json' type as it can be parsed by default 'transformResponse' function.
            if (config.responseType !== 'json') {
              throw e;
            }
          }
        }

        // Handle progress if needed
        if (typeof config.onDownloadProgress === 'function') {
          request.addEventListener('progress', config.onDownloadProgress);
        }

        // Not all browsers support upload events
        if (typeof config.onUploadProgress === 'function' && request.upload) {
          request.upload.addEventListener('progress', config.onUploadProgress);
        }

        if (config.cancelToken) {
          // Handle cancellation
          config.cancelToken.promise.then(function onCanceled(cancel) {
            if (!request) {
              return;
            }

            request.abort();
            reject(cancel);
            // Clean up request
            request = null;
          });
        }

        if (!requestData) {
          requestData = null;
        }

        // Send the request
        request.send(requestData);
      });
    };

    var DEFAULT_CONTENT_TYPE = {
      'Content-Type': 'application/x-www-form-urlencoded'
    };

    function setContentTypeIfUnset(headers, value) {
      if (!utils.isUndefined(headers) && utils.isUndefined(headers['Content-Type'])) {
        headers['Content-Type'] = value;
      }
    }

    function getDefaultAdapter() {
      var adapter;
      if (typeof XMLHttpRequest !== 'undefined') {
        // For browsers use XHR adapter
        adapter = xhr;
      } else if (typeof process !== 'undefined' && Object.prototype.toString.call(process) === '[object process]') {
        // For node use HTTP adapter
        adapter = xhr;
      }
      return adapter;
    }

    var defaults = {
      adapter: getDefaultAdapter(),

      transformRequest: [function transformRequest(data, headers) {
        normalizeHeaderName(headers, 'Accept');
        normalizeHeaderName(headers, 'Content-Type');
        if (utils.isFormData(data) ||
          utils.isArrayBuffer(data) ||
          utils.isBuffer(data) ||
          utils.isStream(data) ||
          utils.isFile(data) ||
          utils.isBlob(data)
        ) {
          return data;
        }
        if (utils.isArrayBufferView(data)) {
          return data.buffer;
        }
        if (utils.isURLSearchParams(data)) {
          setContentTypeIfUnset(headers, 'application/x-www-form-urlencoded;charset=utf-8');
          return data.toString();
        }
        if (utils.isObject(data)) {
          setContentTypeIfUnset(headers, 'application/json;charset=utf-8');
          return JSON.stringify(data);
        }
        return data;
      }],

      transformResponse: [function transformResponse(data) {
        /*eslint no-param-reassign:0*/
        if (typeof data === 'string') {
          try {
            data = JSON.parse(data);
          } catch (e) { /* Ignore */ }
        }
        return data;
      }],

      /**
       * A timeout in milliseconds to abort a request. If set to 0 (default) a
       * timeout is not created.
       */
      timeout: 0,

      xsrfCookieName: 'XSRF-TOKEN',
      xsrfHeaderName: 'X-XSRF-TOKEN',

      maxContentLength: -1,
      maxBodyLength: -1,

      validateStatus: function validateStatus(status) {
        return status >= 200 && status < 300;
      }
    };

    defaults.headers = {
      common: {
        'Accept': 'application/json, text/plain, */*'
      }
    };

    utils.forEach(['delete', 'get', 'head'], function forEachMethodNoData(method) {
      defaults.headers[method] = {};
    });

    utils.forEach(['post', 'put', 'patch'], function forEachMethodWithData(method) {
      defaults.headers[method] = utils.merge(DEFAULT_CONTENT_TYPE);
    });

    var defaults_1 = defaults;

    /**
     * Throws a `Cancel` if cancellation has been requested.
     */
    function throwIfCancellationRequested(config) {
      if (config.cancelToken) {
        config.cancelToken.throwIfRequested();
      }
    }

    /**
     * Dispatch a request to the server using the configured adapter.
     *
     * @param {object} config The config that is to be used for the request
     * @returns {Promise} The Promise to be fulfilled
     */
    var dispatchRequest = function dispatchRequest(config) {
      throwIfCancellationRequested(config);

      // Ensure headers exist
      config.headers = config.headers || {};

      // Transform request data
      config.data = transformData(
        config.data,
        config.headers,
        config.transformRequest
      );

      // Flatten headers
      config.headers = utils.merge(
        config.headers.common || {},
        config.headers[config.method] || {},
        config.headers
      );

      utils.forEach(
        ['delete', 'get', 'head', 'post', 'put', 'patch', 'common'],
        function cleanHeaderConfig(method) {
          delete config.headers[method];
        }
      );

      var adapter = config.adapter || defaults_1.adapter;

      return adapter(config).then(function onAdapterResolution(response) {
        throwIfCancellationRequested(config);

        // Transform response data
        response.data = transformData(
          response.data,
          response.headers,
          config.transformResponse
        );

        return response;
      }, function onAdapterRejection(reason) {
        if (!isCancel(reason)) {
          throwIfCancellationRequested(config);

          // Transform response data
          if (reason && reason.response) {
            reason.response.data = transformData(
              reason.response.data,
              reason.response.headers,
              config.transformResponse
            );
          }
        }

        return Promise.reject(reason);
      });
    };

    /**
     * Config-specific merge-function which creates a new config-object
     * by merging two configuration objects together.
     *
     * @param {Object} config1
     * @param {Object} config2
     * @returns {Object} New object resulting from merging config2 to config1
     */
    var mergeConfig = function mergeConfig(config1, config2) {
      // eslint-disable-next-line no-param-reassign
      config2 = config2 || {};
      var config = {};

      var valueFromConfig2Keys = ['url', 'method', 'data'];
      var mergeDeepPropertiesKeys = ['headers', 'auth', 'proxy', 'params'];
      var defaultToConfig2Keys = [
        'baseURL', 'transformRequest', 'transformResponse', 'paramsSerializer',
        'timeout', 'timeoutMessage', 'withCredentials', 'adapter', 'responseType', 'xsrfCookieName',
        'xsrfHeaderName', 'onUploadProgress', 'onDownloadProgress', 'decompress',
        'maxContentLength', 'maxBodyLength', 'maxRedirects', 'transport', 'httpAgent',
        'httpsAgent', 'cancelToken', 'socketPath', 'responseEncoding'
      ];
      var directMergeKeys = ['validateStatus'];

      function getMergedValue(target, source) {
        if (utils.isPlainObject(target) && utils.isPlainObject(source)) {
          return utils.merge(target, source);
        } else if (utils.isPlainObject(source)) {
          return utils.merge({}, source);
        } else if (utils.isArray(source)) {
          return source.slice();
        }
        return source;
      }

      function mergeDeepProperties(prop) {
        if (!utils.isUndefined(config2[prop])) {
          config[prop] = getMergedValue(config1[prop], config2[prop]);
        } else if (!utils.isUndefined(config1[prop])) {
          config[prop] = getMergedValue(undefined, config1[prop]);
        }
      }

      utils.forEach(valueFromConfig2Keys, function valueFromConfig2(prop) {
        if (!utils.isUndefined(config2[prop])) {
          config[prop] = getMergedValue(undefined, config2[prop]);
        }
      });

      utils.forEach(mergeDeepPropertiesKeys, mergeDeepProperties);

      utils.forEach(defaultToConfig2Keys, function defaultToConfig2(prop) {
        if (!utils.isUndefined(config2[prop])) {
          config[prop] = getMergedValue(undefined, config2[prop]);
        } else if (!utils.isUndefined(config1[prop])) {
          config[prop] = getMergedValue(undefined, config1[prop]);
        }
      });

      utils.forEach(directMergeKeys, function merge(prop) {
        if (prop in config2) {
          config[prop] = getMergedValue(config1[prop], config2[prop]);
        } else if (prop in config1) {
          config[prop] = getMergedValue(undefined, config1[prop]);
        }
      });

      var axiosKeys = valueFromConfig2Keys
        .concat(mergeDeepPropertiesKeys)
        .concat(defaultToConfig2Keys)
        .concat(directMergeKeys);

      var otherKeys = Object
        .keys(config1)
        .concat(Object.keys(config2))
        .filter(function filterAxiosKeys(key) {
          return axiosKeys.indexOf(key) === -1;
        });

      utils.forEach(otherKeys, mergeDeepProperties);

      return config;
    };

    /**
     * Create a new instance of Axios
     *
     * @param {Object} instanceConfig The default config for the instance
     */
    function Axios(instanceConfig) {
      this.defaults = instanceConfig;
      this.interceptors = {
        request: new InterceptorManager_1(),
        response: new InterceptorManager_1()
      };
    }

    /**
     * Dispatch a request
     *
     * @param {Object} config The config specific for this request (merged with this.defaults)
     */
    Axios.prototype.request = function request(config) {
      /*eslint no-param-reassign:0*/
      // Allow for axios('example/url'[, config]) a la fetch API
      if (typeof config === 'string') {
        config = arguments[1] || {};
        config.url = arguments[0];
      } else {
        config = config || {};
      }

      config = mergeConfig(this.defaults, config);

      // Set config.method
      if (config.method) {
        config.method = config.method.toLowerCase();
      } else if (this.defaults.method) {
        config.method = this.defaults.method.toLowerCase();
      } else {
        config.method = 'get';
      }

      // Hook up interceptors middleware
      var chain = [dispatchRequest, undefined];
      var promise = Promise.resolve(config);

      this.interceptors.request.forEach(function unshiftRequestInterceptors(interceptor) {
        chain.unshift(interceptor.fulfilled, interceptor.rejected);
      });

      this.interceptors.response.forEach(function pushResponseInterceptors(interceptor) {
        chain.push(interceptor.fulfilled, interceptor.rejected);
      });

      while (chain.length) {
        promise = promise.then(chain.shift(), chain.shift());
      }

      return promise;
    };

    Axios.prototype.getUri = function getUri(config) {
      config = mergeConfig(this.defaults, config);
      return buildURL(config.url, config.params, config.paramsSerializer).replace(/^\?/, '');
    };

    // Provide aliases for supported request methods
    utils.forEach(['delete', 'get', 'head', 'options'], function forEachMethodNoData(method) {
      /*eslint func-names:0*/
      Axios.prototype[method] = function(url, config) {
        return this.request(mergeConfig(config || {}, {
          method: method,
          url: url,
          data: (config || {}).data
        }));
      };
    });

    utils.forEach(['post', 'put', 'patch'], function forEachMethodWithData(method) {
      /*eslint func-names:0*/
      Axios.prototype[method] = function(url, data, config) {
        return this.request(mergeConfig(config || {}, {
          method: method,
          url: url,
          data: data
        }));
      };
    });

    var Axios_1 = Axios;

    /**
     * A `Cancel` is an object that is thrown when an operation is canceled.
     *
     * @class
     * @param {string=} message The message.
     */
    function Cancel(message) {
      this.message = message;
    }

    Cancel.prototype.toString = function toString() {
      return 'Cancel' + (this.message ? ': ' + this.message : '');
    };

    Cancel.prototype.__CANCEL__ = true;

    var Cancel_1 = Cancel;

    /**
     * A `CancelToken` is an object that can be used to request cancellation of an operation.
     *
     * @class
     * @param {Function} executor The executor function.
     */
    function CancelToken(executor) {
      if (typeof executor !== 'function') {
        throw new TypeError('executor must be a function.');
      }

      var resolvePromise;
      this.promise = new Promise(function promiseExecutor(resolve) {
        resolvePromise = resolve;
      });

      var token = this;
      executor(function cancel(message) {
        if (token.reason) {
          // Cancellation has already been requested
          return;
        }

        token.reason = new Cancel_1(message);
        resolvePromise(token.reason);
      });
    }

    /**
     * Throws a `Cancel` if cancellation has been requested.
     */
    CancelToken.prototype.throwIfRequested = function throwIfRequested() {
      if (this.reason) {
        throw this.reason;
      }
    };

    /**
     * Returns an object that contains a new `CancelToken` and a function that, when called,
     * cancels the `CancelToken`.
     */
    CancelToken.source = function source() {
      var cancel;
      var token = new CancelToken(function executor(c) {
        cancel = c;
      });
      return {
        token: token,
        cancel: cancel
      };
    };

    var CancelToken_1 = CancelToken;

    /**
     * Syntactic sugar for invoking a function and expanding an array for arguments.
     *
     * Common use case would be to use `Function.prototype.apply`.
     *
     *  ```js
     *  function f(x, y, z) {}
     *  var args = [1, 2, 3];
     *  f.apply(null, args);
     *  ```
     *
     * With `spread` this example can be re-written.
     *
     *  ```js
     *  spread(function(x, y, z) {})([1, 2, 3]);
     *  ```
     *
     * @param {Function} callback
     * @returns {Function}
     */
    var spread = function spread(callback) {
      return function wrap(arr) {
        return callback.apply(null, arr);
      };
    };

    /**
     * Determines whether the payload is an error thrown by Axios
     *
     * @param {*} payload The value to test
     * @returns {boolean} True if the payload is an error thrown by Axios, otherwise false
     */
    var isAxiosError = function isAxiosError(payload) {
      return (typeof payload === 'object') && (payload.isAxiosError === true);
    };

    /**
     * Create an instance of Axios
     *
     * @param {Object} defaultConfig The default config for the instance
     * @return {Axios} A new instance of Axios
     */
    function createInstance(defaultConfig) {
      var context = new Axios_1(defaultConfig);
      var instance = bind(Axios_1.prototype.request, context);

      // Copy axios.prototype to instance
      utils.extend(instance, Axios_1.prototype, context);

      // Copy context to instance
      utils.extend(instance, context);

      return instance;
    }

    // Create the default instance to be exported
    var axios = createInstance(defaults_1);

    // Expose Axios class to allow class inheritance
    axios.Axios = Axios_1;

    // Factory for creating new instances
    axios.create = function create(instanceConfig) {
      return createInstance(mergeConfig(axios.defaults, instanceConfig));
    };

    // Expose Cancel & CancelToken
    axios.Cancel = Cancel_1;
    axios.CancelToken = CancelToken_1;
    axios.isCancel = isCancel;

    // Expose all/spread
    axios.all = function all(promises) {
      return Promise.all(promises);
    };
    axios.spread = spread;

    // Expose isAxiosError
    axios.isAxiosError = isAxiosError;

    var axios_1 = axios;

    // Allow use of default import syntax in TypeScript
    var _default = axios;
    axios_1.default = _default;

    var axios$1 = axios_1;

    /**
     * Submission class.
     *
     * @since 1.0.0
     */
    class Request {
        /**
         * Submission data.
         *
         * @param submissionData Submission data.
         *
         * @since 1.0.0
         */
        constructor(url, method, data, nonce) {
            this.url = url;
            this.method = method;
            this.data = data;
            this.nonce = nonce;
        }
        send() {
            let method = this.method.toUpperCase();
            let result;
            switch (method) {
                case 'POST':
                    result = this.post();
                    break;
                case 'GET':
                    result = this.get();
                    break;
                case 'PUT':
                    result = this.put();
                    break;
            }
            return result;
        }
        ;
        post() {
            const options = {
                headers: { 'X-WP-Nonce': this.nonce }
            };
            return axios$1.post(this.url, { data: this.data }, options);
        }
        get() {
            return axios$1({
                method: 'GET',
                headers: {
                    'content-type': 'application/json',
                    'X-WP-Nonce': this.nonce
                },
                url: this.url,
                data: {
                    data: this.data
                }
            });
        }
        put() {
            return axios$1({
                method: 'PUT',
                headers: {
                    'content-type': 'application/json',
                    'X-WP-Nonce': this.nonce
                },
                url: this.url,
                data: {
                    nonce: this.nonce,
                    data: this.data
                }
            });
        }
    }

    class Navigation {
        /**
         * Initializing navigation.
         *
         * @param form  Form object.
         * @param start Name of start fieldset.
         * @param nonce Nonce string;
         *
         * @since 1.0.0
         */
        constructor(form, startFieldset, nonce) {
            this.recentFieldsets = [];
            this.form = form;
            this.nonce = nonce;
            this.setCurrentFieldset(startFieldset);
        }
        /**
         * Get last action.
         *
         * @return Last action (prev or next).
         *
         * @since 1.0.0
         */
        getLastAction() {
            return this.lastAction;
        }
        /**
         * Set current fieldset.
         *
         * @param name Name of fieldset.
         *
         * @since 1.0.0
         */
        setCurrentFieldset(name) {
            let currentFieldset = this.form.getFieldset(name);
            if (currentFieldset === undefined) {
                throw new Error('Cant set current fieldset to "' + name + '". Fieldset name does not exist.');
            }
            else {
                this.currentFieldset = currentFieldset;
            }
            return this;
        }
        /**
         * Get current fieldset.
         *
         * @return Current fieldset.
         *
         * @since 1.0.0
         */
        getCurrentFieldset() {
            return this.currentFieldset;
        }
        /**
         * Set previous fieldset.
         *
         * @return Navigation object.
         *
         * @since 1.0.0
         */
        prevFieldset() {
            if (!this.hasPrevFieldset()) {
                return this;
            }
            this.lastAction = 'prev';
            this.setCurrentFieldset(this.recentFieldsets.pop());
            return this;
        }
        /**
         * Set next fieldset.
         *
         * @return Navigation object.
         *
         * @since 1.0.0
         */
        nextFieldset() {
            this.currentFieldset.validate();
            if (this.currentFieldset.hasValidationErrors()) {
                return this;
            }
            let nextFieldset = this.getNextFieldset();
            if (nextFieldset !== undefined) {
                this.recentFieldsets.push(this.currentFieldset.name);
                this.lastAction = 'next';
                this.setCurrentFieldset(nextFieldset.name);
                return this;
            }
            throw new Error('No next fieldset not found.');
        }
        /**
         * Is there a previous fieldset?
         *
         * @return True if there is a previous fieldset, false if not.
         *
         * @since 1.0.0
         */
        hasPrevFieldset() {
            return this.recentFieldsets.length > 0;
        }
        /**
         * Is there a next fieldset?
         *
         * @return True if there is a previous fieldset, false if not.
         *
         * @since 1.0.0
         */
        hasNextFieldset() {
            if (this.currentFieldset.nextFieldset !== undefined) {
                return true;
            }
            let nextFieldset = this.getNextFieldset();
            if (nextFieldset !== undefined) {
                return true;
            }
            return false;
        }
        /**
         * Is there a submission to do?
         *
         * @return True if there is a submissionto do, false if not.
         *
         * @since 1.0.0
         */
        hasSubmission() {
            return this.currentFieldset.hasSubmission();
        }
        prepareData() {
            let fieldsets = this.form.fieldsets;
            let fieldsetsToSend = this.currentFieldset.submission.fieldsets;
            let data = [];
            fieldsets.forEach((fieldset) => {
                if (fieldsetsToSend.includes(fieldset.name)) {
                    let fields = [];
                    fieldset.fields.forEach((field) => {
                        fields.push({
                            label: field.label,
                            value: field.value
                        });
                    });
                    data.push({
                        label: fieldset.label,
                        fields: fields
                    });
                }
            });
            return data;
        }
        submit() {
            this.currentFieldset.validate();
            if (this.currentFieldset.hasValidationErrors()) {
                return new Promise((resolve, reject) => {
                    reject('validationError');
                });
            }
            let request = new Request(this.currentFieldset.submission.url, this.currentFieldset.submission.method, this.prepareData(), this.nonce);
            return request.send();
        }
        /**
         * Returns the next fieldset.
         *
         * @return Next fieldset object.
         *
         * @since 1.0.0
         */
        getNextFieldset() {
            if (this.currentFieldset.nextFieldset !== undefined) {
                return this.form.getFieldset(this.currentFieldset.nextFieldset);
            }
            let nextFieldsets = this.getPossibleNextFieldsets();
            let nextFieldset;
            if (nextFieldsets.length === 0) {
                return nextFieldset;
            }
            nextFieldsets.forEach((fieldset) => {
                if (fieldset.conditionsFullfilled() && nextFieldset === undefined) {
                    nextFieldset = fieldset;
                }
            });
            return nextFieldset;
        }
        /**
         * Returns the previous fieldset.
         *
         * @return Previous fieldset object.
         *
         * @since 1.0.0
         */
        getPrevFieldset() {
            if (!this.hasPrevFieldset()) {
                throw Error('There is no previous fieldset');
            }
            let preFieldsetName = this.recentFieldsets[this.recentFieldsets.length - 1];
            return this.form.getFieldset(preFieldsetName);
        }
        /**
         * Returns a possible fieldsets.
         *
         * Possible fieldsets are all fieldsets which containing a prevFieldset, containing the current fieldset.
         *
         * @return An array of Fieldsets.
         *
         * @since 1.0.0
         */
        getPossibleNextFieldsets() {
            let nextFieldsets = this.form.fieldsets.filter((fieldset) => {
                return fieldset.prevFieldset === this.currentFieldset.name;
            });
            return nextFieldsets;
        }
    }

    /**
     * Class Form.
     *
     * @since 1.0.0
     */
    class Form {
        /**
         * Initializing form data.
         *
         * @param formData Formdata from JSON file.
         *
         * @since 1.0.0
         */
        constructor(formData, nonce) {
            this.sent = false;
            this.name = formData.name;
            this.classes = formData.classes;
            this.fieldsets = [];
            formData.fieldsets.forEach((fieldset) => {
                this.fieldsets.push(new Fieldset(this, fieldset));
            });
            this.navigation = new Navigation(this, formData.start, nonce);
        }
        /**
         * Get a specific fieldset.
         *
         * @param name Name of fieldset
         * @return Fieldset
         *
         * @since 1.0.0
         */
        getFieldset(name) {
            let fieldsets = this.fieldsets.filter((fieldset) => {
                return fieldset.name === name;
            });
            return fieldsets[0];
        }
        getField(name) {
            let foundField;
            this.fieldsets.forEach((fieldset) => {
                fieldset.fields.forEach((field) => {
                    if (field.name === name) {
                        foundField = field;
                    }
                });
            });
            return foundField;
        }
        /**
         * Has form validation errors.
         *
         * @return True if field has errors, false if not.
         *
         * @since 1.0.0
         */
        hasValidationErrors() {
            let hasValidationErrors = false;
            this.fieldsets.forEach((fieldset) => {
                if (fieldset.hasValidationErrors()) {
                    hasValidationErrors = true;
                    return;
                }
            });
            return hasValidationErrors;
        }
        /**
         * Get CSS Classes.
         *
         * @return String of CSS classes.
         *
         * @since 1.0.0
         */
        getClasses() {
            if (this.classes.length > 0) {
                return this.classes.join(' ');
            }
            return '';
        }
    }

    /* src/frontend/Components/Fields/Errors.svelte generated by Svelte v3.32.3 */

    const file = "src/frontend/Components/Fields/Errors.svelte";

    function get_each_context(ctx, list, i) {
    	const child_ctx = ctx.slice();
    	child_ctx[1] = list[i];
    	return child_ctx;
    }

    // (4:0) {#if errors !== undefined && errors.length > 0}
    function create_if_block(ctx) {
    	let div;
    	let ul;
    	let each_value = /*errors*/ ctx[0];
    	validate_each_argument(each_value);
    	let each_blocks = [];

    	for (let i = 0; i < each_value.length; i += 1) {
    		each_blocks[i] = create_each_block(get_each_context(ctx, each_value, i));
    	}

    	const block = {
    		c: function create() {
    			div = element("div");
    			ul = element("ul");

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].c();
    			}

    			add_location(ul, file, 5, 4, 122);
    			attr_dev(div, "class", "notices");
    			add_location(div, file, 4, 0, 96);
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, div, anchor);
    			append_dev(div, ul);

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].m(ul, null);
    			}
    		},
    		p: function update(ctx, dirty) {
    			if (dirty & /*errors*/ 1) {
    				each_value = /*errors*/ ctx[0];
    				validate_each_argument(each_value);
    				let i;

    				for (i = 0; i < each_value.length; i += 1) {
    					const child_ctx = get_each_context(ctx, each_value, i);

    					if (each_blocks[i]) {
    						each_blocks[i].p(child_ctx, dirty);
    					} else {
    						each_blocks[i] = create_each_block(child_ctx);
    						each_blocks[i].c();
    						each_blocks[i].m(ul, null);
    					}
    				}

    				for (; i < each_blocks.length; i += 1) {
    					each_blocks[i].d(1);
    				}

    				each_blocks.length = each_value.length;
    			}
    		},
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(div);
    			destroy_each(each_blocks, detaching);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_if_block.name,
    		type: "if",
    		source: "(4:0) {#if errors !== undefined && errors.length > 0}",
    		ctx
    	});

    	return block;
    }

    // (7:4) {#each errors as error}
    function create_each_block(ctx) {
    	let li;
    	let t_value = /*error*/ ctx[1] + "";
    	let t;

    	const block = {
    		c: function create() {
    			li = element("li");
    			t = text(t_value);
    			add_location(li, file, 7, 8, 163);
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, li, anchor);
    			append_dev(li, t);
    		},
    		p: function update(ctx, dirty) {
    			if (dirty & /*errors*/ 1 && t_value !== (t_value = /*error*/ ctx[1] + "")) set_data_dev(t, t_value);
    		},
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(li);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_each_block.name,
    		type: "each",
    		source: "(7:4) {#each errors as error}",
    		ctx
    	});

    	return block;
    }

    function create_fragment(ctx) {
    	let if_block_anchor;
    	let if_block = /*errors*/ ctx[0] !== undefined && /*errors*/ ctx[0].length > 0 && create_if_block(ctx);

    	const block = {
    		c: function create() {
    			if (if_block) if_block.c();
    			if_block_anchor = empty();
    		},
    		l: function claim(nodes) {
    			throw new Error("options.hydrate only works if the component was compiled with the `hydratable: true` option");
    		},
    		m: function mount(target, anchor) {
    			if (if_block) if_block.m(target, anchor);
    			insert_dev(target, if_block_anchor, anchor);
    		},
    		p: function update(ctx, [dirty]) {
    			if (/*errors*/ ctx[0] !== undefined && /*errors*/ ctx[0].length > 0) {
    				if (if_block) {
    					if_block.p(ctx, dirty);
    				} else {
    					if_block = create_if_block(ctx);
    					if_block.c();
    					if_block.m(if_block_anchor.parentNode, if_block_anchor);
    				}
    			} else if (if_block) {
    				if_block.d(1);
    				if_block = null;
    			}
    		},
    		i: noop,
    		o: noop,
    		d: function destroy(detaching) {
    			if (if_block) if_block.d(detaching);
    			if (detaching) detach_dev(if_block_anchor);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_fragment.name,
    		type: "component",
    		source: "",
    		ctx
    	});

    	return block;
    }

    function instance($$self, $$props, $$invalidate) {
    	let { $$slots: slots = {}, $$scope } = $$props;
    	validate_slots("Errors", slots, []);
    	let { errors } = $$props;
    	const writable_props = ["errors"];

    	Object.keys($$props).forEach(key => {
    		if (!~writable_props.indexOf(key) && key.slice(0, 2) !== "$$") console.warn(`<Errors> was created with unknown prop '${key}'`);
    	});

    	$$self.$$set = $$props => {
    		if ("errors" in $$props) $$invalidate(0, errors = $$props.errors);
    	};

    	$$self.$capture_state = () => ({ errors });

    	$$self.$inject_state = $$props => {
    		if ("errors" in $$props) $$invalidate(0, errors = $$props.errors);
    	};

    	if ($$props && "$$inject" in $$props) {
    		$$self.$inject_state($$props.$$inject);
    	}

    	return [errors];
    }

    class Errors extends SvelteComponentDev {
    	constructor(options) {
    		super(options);
    		init(this, options, instance, create_fragment, safe_not_equal, { errors: 0 });

    		dispatch_dev("SvelteRegisterComponent", {
    			component: this,
    			tagName: "Errors",
    			options,
    			id: create_fragment.name
    		});

    		const { ctx } = this.$$;
    		const props = options.props || {};

    		if (/*errors*/ ctx[0] === undefined && !("errors" in props)) {
    			console.warn("<Errors> was created without expected prop 'errors'");
    		}
    	}

    	get errors() {
    		throw new Error("<Errors>: Props cannot be read directly from the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}

    	set errors(value) {
    		throw new Error("<Errors>: Props cannot be set directly on the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}
    }

    /* src/frontend/Components/Fields/Text.svelte generated by Svelte v3.32.3 */
    const file$1 = "src/frontend/Components/Fields/Text.svelte";

    function create_fragment$1(ctx) {
    	let section;
    	let label;
    	let t0_value = /*field*/ ctx[0].label + "";
    	let t0;
    	let t1;
    	let input;
    	let input_placeholder_value;
    	let t2;
    	let errors_1;
    	let section_class_value;
    	let current;
    	let mounted;
    	let dispose;

    	errors_1 = new Errors({
    			props: { errors: /*errors*/ ctx[1] },
    			$$inline: true
    		});

    	const block = {
    		c: function create() {
    			section = element("section");
    			label = element("label");
    			t0 = text(t0_value);
    			t1 = space();
    			input = element("input");
    			t2 = space();
    			create_component(errors_1.$$.fragment);
    			attr_dev(input, "type", "text");
    			attr_dev(input, "placeholder", input_placeholder_value = /*field*/ ctx[0].placeholder);
    			add_location(input, file$1, 14, 8, 376);
    			add_location(label, file$1, 12, 4, 338);
    			attr_dev(section, "class", section_class_value = "text " + /*field*/ ctx[0].getClasses());
    			add_location(section, file$1, 11, 0, 290);
    		},
    		l: function claim(nodes) {
    			throw new Error("options.hydrate only works if the component was compiled with the `hydratable: true` option");
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, section, anchor);
    			append_dev(section, label);
    			append_dev(label, t0);
    			append_dev(label, t1);
    			append_dev(label, input);
    			set_input_value(input, /*field*/ ctx[0].value);
    			append_dev(section, t2);
    			mount_component(errors_1, section, null);
    			current = true;

    			if (!mounted) {
    				dispose = [
    					listen_dev(input, "input", /*input_input_handler*/ ctx[3]),
    					listen_dev(input, "blur", /*setValue*/ ctx[2], false, false, false)
    				];

    				mounted = true;
    			}
    		},
    		p: function update(ctx, [dirty]) {
    			if ((!current || dirty & /*field*/ 1) && t0_value !== (t0_value = /*field*/ ctx[0].label + "")) set_data_dev(t0, t0_value);

    			if (!current || dirty & /*field*/ 1 && input_placeholder_value !== (input_placeholder_value = /*field*/ ctx[0].placeholder)) {
    				attr_dev(input, "placeholder", input_placeholder_value);
    			}

    			if (dirty & /*field*/ 1 && input.value !== /*field*/ ctx[0].value) {
    				set_input_value(input, /*field*/ ctx[0].value);
    			}

    			const errors_1_changes = {};
    			if (dirty & /*errors*/ 2) errors_1_changes.errors = /*errors*/ ctx[1];
    			errors_1.$set(errors_1_changes);

    			if (!current || dirty & /*field*/ 1 && section_class_value !== (section_class_value = "text " + /*field*/ ctx[0].getClasses())) {
    				attr_dev(section, "class", section_class_value);
    			}
    		},
    		i: function intro(local) {
    			if (current) return;
    			transition_in(errors_1.$$.fragment, local);
    			current = true;
    		},
    		o: function outro(local) {
    			transition_out(errors_1.$$.fragment, local);
    			current = false;
    		},
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(section);
    			destroy_component(errors_1);
    			mounted = false;
    			run_all(dispose);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_fragment$1.name,
    		type: "component",
    		source: "",
    		ctx
    	});

    	return block;
    }

    function instance$1($$self, $$props, $$invalidate) {
    	let errors;
    	let { $$slots: slots = {}, $$scope } = $$props;
    	validate_slots("Text", slots, []);
    	
    	let { field } = $$props;
    	const dispatch = createEventDispatcher();

    	const setValue = () => {
    		dispatch("update", field.fieldset.form);
    	};

    	const writable_props = ["field"];

    	Object.keys($$props).forEach(key => {
    		if (!~writable_props.indexOf(key) && key.slice(0, 2) !== "$$") console.warn(`<Text> was created with unknown prop '${key}'`);
    	});

    	function input_input_handler() {
    		field.value = this.value;
    		$$invalidate(0, field);
    	}

    	$$self.$$set = $$props => {
    		if ("field" in $$props) $$invalidate(0, field = $$props.field);
    	};

    	$$self.$capture_state = () => ({
    		createEventDispatcher,
    		Errors,
    		field,
    		dispatch,
    		setValue,
    		errors
    	});

    	$$self.$inject_state = $$props => {
    		if ("field" in $$props) $$invalidate(0, field = $$props.field);
    		if ("errors" in $$props) $$invalidate(1, errors = $$props.errors);
    	};

    	if ($$props && "$$inject" in $$props) {
    		$$self.$inject_state($$props.$$inject);
    	}

    	$$self.$$.update = () => {
    		if ($$self.$$.dirty & /*field*/ 1) {
    			$$invalidate(1, errors = field.getValidationErors());
    		}
    	};

    	return [field, errors, setValue, input_input_handler];
    }

    class Text extends SvelteComponentDev {
    	constructor(options) {
    		super(options);
    		init(this, options, instance$1, create_fragment$1, safe_not_equal, { field: 0 });

    		dispatch_dev("SvelteRegisterComponent", {
    			component: this,
    			tagName: "Text",
    			options,
    			id: create_fragment$1.name
    		});

    		const { ctx } = this.$$;
    		const props = options.props || {};

    		if (/*field*/ ctx[0] === undefined && !("field" in props)) {
    			console.warn("<Text> was created without expected prop 'field'");
    		}
    	}

    	get field() {
    		throw new Error("<Text>: Props cannot be read directly from the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}

    	set field(value) {
    		throw new Error("<Text>: Props cannot be set directly on the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}
    }

    /* src/frontend/Components/Fields/Textarea.svelte generated by Svelte v3.32.3 */
    const file$2 = "src/frontend/Components/Fields/Textarea.svelte";

    function create_fragment$2(ctx) {
    	let div;
    	let label;
    	let t0_value = /*field*/ ctx[0].label + "";
    	let t0;
    	let t1;
    	let textarea;
    	let textarea_placeholder_value;
    	let t2;
    	let errors_1;
    	let current;
    	let mounted;
    	let dispose;

    	errors_1 = new Errors({
    			props: { errors: /*errors*/ ctx[1] },
    			$$inline: true
    		});

    	const block = {
    		c: function create() {
    			div = element("div");
    			label = element("label");
    			t0 = text(t0_value);
    			t1 = space();
    			textarea = element("textarea");
    			t2 = space();
    			create_component(errors_1.$$.fragment);
    			attr_dev(textarea, "placeholder", textarea_placeholder_value = /*field*/ ctx[0].placeholder);
    			add_location(textarea, file$2, 14, 8, 355);
    			add_location(label, file$2, 12, 4, 317);
    			attr_dev(div, "class", "textarea");
    			add_location(div, file$2, 11, 0, 290);
    		},
    		l: function claim(nodes) {
    			throw new Error("options.hydrate only works if the component was compiled with the `hydratable: true` option");
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, div, anchor);
    			append_dev(div, label);
    			append_dev(label, t0);
    			append_dev(label, t1);
    			append_dev(label, textarea);
    			set_input_value(textarea, /*field*/ ctx[0].value);
    			append_dev(div, t2);
    			mount_component(errors_1, div, null);
    			current = true;

    			if (!mounted) {
    				dispose = [
    					listen_dev(textarea, "input", /*textarea_input_handler*/ ctx[3]),
    					listen_dev(textarea, "blur", /*setValue*/ ctx[2], false, false, false)
    				];

    				mounted = true;
    			}
    		},
    		p: function update(ctx, [dirty]) {
    			if ((!current || dirty & /*field*/ 1) && t0_value !== (t0_value = /*field*/ ctx[0].label + "")) set_data_dev(t0, t0_value);

    			if (!current || dirty & /*field*/ 1 && textarea_placeholder_value !== (textarea_placeholder_value = /*field*/ ctx[0].placeholder)) {
    				attr_dev(textarea, "placeholder", textarea_placeholder_value);
    			}

    			if (dirty & /*field*/ 1) {
    				set_input_value(textarea, /*field*/ ctx[0].value);
    			}

    			const errors_1_changes = {};
    			if (dirty & /*errors*/ 2) errors_1_changes.errors = /*errors*/ ctx[1];
    			errors_1.$set(errors_1_changes);
    		},
    		i: function intro(local) {
    			if (current) return;
    			transition_in(errors_1.$$.fragment, local);
    			current = true;
    		},
    		o: function outro(local) {
    			transition_out(errors_1.$$.fragment, local);
    			current = false;
    		},
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(div);
    			destroy_component(errors_1);
    			mounted = false;
    			run_all(dispose);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_fragment$2.name,
    		type: "component",
    		source: "",
    		ctx
    	});

    	return block;
    }

    function instance$2($$self, $$props, $$invalidate) {
    	let errors;
    	let { $$slots: slots = {}, $$scope } = $$props;
    	validate_slots("Textarea", slots, []);
    	
    	let { field } = $$props;
    	const dispatch = createEventDispatcher();

    	const setValue = () => {
    		dispatch("update", field.fieldset.form);
    	};

    	const writable_props = ["field"];

    	Object.keys($$props).forEach(key => {
    		if (!~writable_props.indexOf(key) && key.slice(0, 2) !== "$$") console.warn(`<Textarea> was created with unknown prop '${key}'`);
    	});

    	function textarea_input_handler() {
    		field.value = this.value;
    		$$invalidate(0, field);
    	}

    	$$self.$$set = $$props => {
    		if ("field" in $$props) $$invalidate(0, field = $$props.field);
    	};

    	$$self.$capture_state = () => ({
    		createEventDispatcher,
    		Errors,
    		field,
    		dispatch,
    		setValue,
    		errors
    	});

    	$$self.$inject_state = $$props => {
    		if ("field" in $$props) $$invalidate(0, field = $$props.field);
    		if ("errors" in $$props) $$invalidate(1, errors = $$props.errors);
    	};

    	if ($$props && "$$inject" in $$props) {
    		$$self.$inject_state($$props.$$inject);
    	}

    	$$self.$$.update = () => {
    		if ($$self.$$.dirty & /*field*/ 1) {
    			$$invalidate(1, errors = field.getValidationErors());
    		}
    	};

    	return [field, errors, setValue, textarea_input_handler];
    }

    class Textarea extends SvelteComponentDev {
    	constructor(options) {
    		super(options);
    		init(this, options, instance$2, create_fragment$2, safe_not_equal, { field: 0 });

    		dispatch_dev("SvelteRegisterComponent", {
    			component: this,
    			tagName: "Textarea",
    			options,
    			id: create_fragment$2.name
    		});

    		const { ctx } = this.$$;
    		const props = options.props || {};

    		if (/*field*/ ctx[0] === undefined && !("field" in props)) {
    			console.warn("<Textarea> was created without expected prop 'field'");
    		}
    	}

    	get field() {
    		throw new Error("<Textarea>: Props cannot be read directly from the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}

    	set field(value) {
    		throw new Error("<Textarea>: Props cannot be set directly on the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}
    }

    /* src/frontend/Components/Fields/Range.svelte generated by Svelte v3.32.3 */
    const file$3 = "src/frontend/Components/Fields/Range.svelte";

    // (15:38) {#if field.params.unit !== undefined}
    function create_if_block$1(ctx) {
    	let t_value = /*field*/ ctx[0].params.unit + "";
    	let t;

    	const block = {
    		c: function create() {
    			t = text(t_value);
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, t, anchor);
    		},
    		p: function update(ctx, dirty) {
    			if (dirty & /*field*/ 1 && t_value !== (t_value = /*field*/ ctx[0].params.unit + "")) set_data_dev(t, t_value);
    		},
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(t);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_if_block$1.name,
    		type: "if",
    		source: "(15:38) {#if field.params.unit !== undefined}",
    		ctx
    	});

    	return block;
    }

    function create_fragment$3(ctx) {
    	let section;
    	let label;
    	let t0_value = /*field*/ ctx[0].label + "";
    	let t0;
    	let t1;
    	let t2_value = /*field*/ ctx[0].value + "";
    	let t2;
    	let t3;
    	let label_for_value;
    	let t4;
    	let input;
    	let input_name_value;
    	let input_min_value;
    	let input_max_value;
    	let input_step_value;
    	let t5;
    	let errors_1;
    	let section_class_value;
    	let current;
    	let mounted;
    	let dispose;
    	let if_block = /*field*/ ctx[0].params.unit !== undefined && create_if_block$1(ctx);

    	errors_1 = new Errors({
    			props: { errors: /*errors*/ ctx[1] },
    			$$inline: true
    		});

    	const block = {
    		c: function create() {
    			section = element("section");
    			label = element("label");
    			t0 = text(t0_value);
    			t1 = text(":  ");
    			t2 = text(t2_value);
    			t3 = space();
    			if (if_block) if_block.c();
    			t4 = space();
    			input = element("input");
    			t5 = space();
    			create_component(errors_1.$$.fragment);
    			attr_dev(label, "for", label_for_value = /*field*/ ctx[0].name);
    			add_location(label, file$3, 13, 4, 347);
    			attr_dev(input, "name", input_name_value = /*field*/ ctx[0].name);
    			attr_dev(input, "type", "range");
    			attr_dev(input, "min", input_min_value = /*field*/ ctx[0].params.min);
    			attr_dev(input, "max", input_max_value = /*field*/ ctx[0].params.max);
    			attr_dev(input, "step", input_step_value = /*field*/ ctx[0].params.step);
    			add_location(input, file$3, 16, 4, 491);
    			attr_dev(section, "class", section_class_value = "range " + /*field*/ ctx[0].getClasses());
    			add_location(section, file$3, 12, 0, 298);
    		},
    		l: function claim(nodes) {
    			throw new Error("options.hydrate only works if the component was compiled with the `hydratable: true` option");
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, section, anchor);
    			append_dev(section, label);
    			append_dev(label, t0);
    			append_dev(label, t1);
    			append_dev(label, t2);
    			append_dev(label, t3);
    			if (if_block) if_block.m(label, null);
    			append_dev(section, t4);
    			append_dev(section, input);
    			set_input_value(input, /*field*/ ctx[0].value);
    			append_dev(section, t5);
    			mount_component(errors_1, section, null);
    			current = true;

    			if (!mounted) {
    				dispose = [
    					listen_dev(input, "change", /*input_change_input_handler*/ ctx[2]),
    					listen_dev(input, "input", /*input_change_input_handler*/ ctx[2])
    				];

    				mounted = true;
    			}
    		},
    		p: function update(ctx, [dirty]) {
    			if ((!current || dirty & /*field*/ 1) && t0_value !== (t0_value = /*field*/ ctx[0].label + "")) set_data_dev(t0, t0_value);
    			if ((!current || dirty & /*field*/ 1) && t2_value !== (t2_value = /*field*/ ctx[0].value + "")) set_data_dev(t2, t2_value);

    			if (/*field*/ ctx[0].params.unit !== undefined) {
    				if (if_block) {
    					if_block.p(ctx, dirty);
    				} else {
    					if_block = create_if_block$1(ctx);
    					if_block.c();
    					if_block.m(label, null);
    				}
    			} else if (if_block) {
    				if_block.d(1);
    				if_block = null;
    			}

    			if (!current || dirty & /*field*/ 1 && label_for_value !== (label_for_value = /*field*/ ctx[0].name)) {
    				attr_dev(label, "for", label_for_value);
    			}

    			if (!current || dirty & /*field*/ 1 && input_name_value !== (input_name_value = /*field*/ ctx[0].name)) {
    				attr_dev(input, "name", input_name_value);
    			}

    			if (!current || dirty & /*field*/ 1 && input_min_value !== (input_min_value = /*field*/ ctx[0].params.min)) {
    				attr_dev(input, "min", input_min_value);
    			}

    			if (!current || dirty & /*field*/ 1 && input_max_value !== (input_max_value = /*field*/ ctx[0].params.max)) {
    				attr_dev(input, "max", input_max_value);
    			}

    			if (!current || dirty & /*field*/ 1 && input_step_value !== (input_step_value = /*field*/ ctx[0].params.step)) {
    				attr_dev(input, "step", input_step_value);
    			}

    			if (dirty & /*field*/ 1) {
    				set_input_value(input, /*field*/ ctx[0].value);
    			}

    			const errors_1_changes = {};
    			if (dirty & /*errors*/ 2) errors_1_changes.errors = /*errors*/ ctx[1];
    			errors_1.$set(errors_1_changes);

    			if (!current || dirty & /*field*/ 1 && section_class_value !== (section_class_value = "range " + /*field*/ ctx[0].getClasses())) {
    				attr_dev(section, "class", section_class_value);
    			}
    		},
    		i: function intro(local) {
    			if (current) return;
    			transition_in(errors_1.$$.fragment, local);
    			current = true;
    		},
    		o: function outro(local) {
    			transition_out(errors_1.$$.fragment, local);
    			current = false;
    		},
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(section);
    			if (if_block) if_block.d();
    			destroy_component(errors_1);
    			mounted = false;
    			run_all(dispose);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_fragment$3.name,
    		type: "component",
    		source: "",
    		ctx
    	});

    	return block;
    }

    function instance$3($$self, $$props, $$invalidate) {
    	let { $$slots: slots = {}, $$scope } = $$props;
    	validate_slots("Range", slots, []);
    	
    	let { field } = $$props;
    	const dispatch = createEventDispatcher();
    	let errors = [];

    	const setValue = () => {
    		$$invalidate(1, errors = field.validate());
    		dispatch("update", field.fieldset.form);
    	};

    	const writable_props = ["field"];

    	Object.keys($$props).forEach(key => {
    		if (!~writable_props.indexOf(key) && key.slice(0, 2) !== "$$") console.warn(`<Range> was created with unknown prop '${key}'`);
    	});

    	function input_change_input_handler() {
    		field.value = to_number(this.value);
    		$$invalidate(0, field);
    	}

    	$$self.$$set = $$props => {
    		if ("field" in $$props) $$invalidate(0, field = $$props.field);
    	};

    	$$self.$capture_state = () => ({
    		createEventDispatcher,
    		Errors,
    		field,
    		dispatch,
    		errors,
    		setValue
    	});

    	$$self.$inject_state = $$props => {
    		if ("field" in $$props) $$invalidate(0, field = $$props.field);
    		if ("errors" in $$props) $$invalidate(1, errors = $$props.errors);
    	};

    	if ($$props && "$$inject" in $$props) {
    		$$self.$inject_state($$props.$$inject);
    	}

    	return [field, errors, input_change_input_handler];
    }

    class Range extends SvelteComponentDev {
    	constructor(options) {
    		super(options);
    		init(this, options, instance$3, create_fragment$3, safe_not_equal, { field: 0 });

    		dispatch_dev("SvelteRegisterComponent", {
    			component: this,
    			tagName: "Range",
    			options,
    			id: create_fragment$3.name
    		});

    		const { ctx } = this.$$;
    		const props = options.props || {};

    		if (/*field*/ ctx[0] === undefined && !("field" in props)) {
    			console.warn("<Range> was created without expected prop 'field'");
    		}
    	}

    	get field() {
    		throw new Error("<Range>: Props cannot be read directly from the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}

    	set field(value) {
    		throw new Error("<Range>: Props cannot be set directly on the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}
    }

    /* src/frontend/Components/Fields/SelectChoice.svelte generated by Svelte v3.32.3 */
    const file$4 = "src/frontend/Components/Fields/SelectChoice.svelte";

    function get_each_context$1(ctx, list, i) {
    	const child_ctx = ctx.slice();
    	child_ctx[5] = list[i];
    	return child_ctx;
    }

    // (18:12) {#each field.choices as choice}
    function create_each_block$1(ctx) {
    	let option;
    	let t_value = /*choice*/ ctx[5].label + "";
    	let t;
    	let option_value_value;

    	const block = {
    		c: function create() {
    			option = element("option");
    			t = text(t_value);
    			option.__value = option_value_value = /*choice*/ ctx[5].value;
    			option.value = option.__value;
    			add_location(option, file$4, 18, 16, 505);
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, option, anchor);
    			append_dev(option, t);
    		},
    		p: function update(ctx, dirty) {
    			if (dirty & /*field*/ 1 && t_value !== (t_value = /*choice*/ ctx[5].label + "")) set_data_dev(t, t_value);

    			if (dirty & /*field*/ 1 && option_value_value !== (option_value_value = /*choice*/ ctx[5].value)) {
    				prop_dev(option, "__value", option_value_value);
    				option.value = option.__value;
    			}
    		},
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(option);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_each_block$1.name,
    		type: "each",
    		source: "(18:12) {#each field.choices as choice}",
    		ctx
    	});

    	return block;
    }

    function create_fragment$4(ctx) {
    	let legend;
    	let t0_value = /*field*/ ctx[0].label + "";
    	let t0;
    	let t1;
    	let div;
    	let label;
    	let t2_value = /*field*/ ctx[0].label + "";
    	let t2;
    	let t3;
    	let select;
    	let t4;
    	let errors_1;
    	let current;
    	let mounted;
    	let dispose;
    	let each_value = /*field*/ ctx[0].choices;
    	validate_each_argument(each_value);
    	let each_blocks = [];

    	for (let i = 0; i < each_value.length; i += 1) {
    		each_blocks[i] = create_each_block$1(get_each_context$1(ctx, each_value, i));
    	}

    	errors_1 = new Errors({
    			props: { errors: /*errors*/ ctx[1] },
    			$$inline: true
    		});

    	const block = {
    		c: function create() {
    			legend = element("legend");
    			t0 = text(t0_value);
    			t1 = space();
    			div = element("div");
    			label = element("label");
    			t2 = text(t2_value);
    			t3 = space();
    			select = element("select");

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].c();
    			}

    			t4 = space();
    			create_component(errors_1.$$.fragment);
    			add_location(legend, file$4, 11, 0, 290);
    			if (/*field*/ ctx[0].value === void 0) add_render_callback(() => /*select_change_handler*/ ctx[3].call(select));
    			add_location(select, file$4, 16, 8, 392);
    			add_location(label, file$4, 14, 4, 354);
    			attr_dev(div, "class", "select-choice");
    			add_location(div, file$4, 13, 0, 322);
    		},
    		l: function claim(nodes) {
    			throw new Error("options.hydrate only works if the component was compiled with the `hydratable: true` option");
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, legend, anchor);
    			append_dev(legend, t0);
    			insert_dev(target, t1, anchor);
    			insert_dev(target, div, anchor);
    			append_dev(div, label);
    			append_dev(label, t2);
    			append_dev(label, t3);
    			append_dev(label, select);

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].m(select, null);
    			}

    			select_option(select, /*field*/ ctx[0].value);
    			append_dev(div, t4);
    			mount_component(errors_1, div, null);
    			current = true;

    			if (!mounted) {
    				dispose = [
    					listen_dev(select, "change", /*select_change_handler*/ ctx[3]),
    					listen_dev(select, "blur", /*setValue*/ ctx[2], false, false, false)
    				];

    				mounted = true;
    			}
    		},
    		p: function update(ctx, [dirty]) {
    			if ((!current || dirty & /*field*/ 1) && t0_value !== (t0_value = /*field*/ ctx[0].label + "")) set_data_dev(t0, t0_value);
    			if ((!current || dirty & /*field*/ 1) && t2_value !== (t2_value = /*field*/ ctx[0].label + "")) set_data_dev(t2, t2_value);

    			if (dirty & /*field*/ 1) {
    				each_value = /*field*/ ctx[0].choices;
    				validate_each_argument(each_value);
    				let i;

    				for (i = 0; i < each_value.length; i += 1) {
    					const child_ctx = get_each_context$1(ctx, each_value, i);

    					if (each_blocks[i]) {
    						each_blocks[i].p(child_ctx, dirty);
    					} else {
    						each_blocks[i] = create_each_block$1(child_ctx);
    						each_blocks[i].c();
    						each_blocks[i].m(select, null);
    					}
    				}

    				for (; i < each_blocks.length; i += 1) {
    					each_blocks[i].d(1);
    				}

    				each_blocks.length = each_value.length;
    			}

    			if (dirty & /*field*/ 1) {
    				select_option(select, /*field*/ ctx[0].value);
    			}

    			const errors_1_changes = {};
    			if (dirty & /*errors*/ 2) errors_1_changes.errors = /*errors*/ ctx[1];
    			errors_1.$set(errors_1_changes);
    		},
    		i: function intro(local) {
    			if (current) return;
    			transition_in(errors_1.$$.fragment, local);
    			current = true;
    		},
    		o: function outro(local) {
    			transition_out(errors_1.$$.fragment, local);
    			current = false;
    		},
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(legend);
    			if (detaching) detach_dev(t1);
    			if (detaching) detach_dev(div);
    			destroy_each(each_blocks, detaching);
    			destroy_component(errors_1);
    			mounted = false;
    			run_all(dispose);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_fragment$4.name,
    		type: "component",
    		source: "",
    		ctx
    	});

    	return block;
    }

    function instance$4($$self, $$props, $$invalidate) {
    	let errors;
    	let { $$slots: slots = {}, $$scope } = $$props;
    	validate_slots("SelectChoice", slots, []);
    	
    	let { field } = $$props;
    	const dispatch = createEventDispatcher();

    	const setValue = () => {
    		dispatch("update", field.fieldset.form);
    	};

    	const writable_props = ["field"];

    	Object.keys($$props).forEach(key => {
    		if (!~writable_props.indexOf(key) && key.slice(0, 2) !== "$$") console.warn(`<SelectChoice> was created with unknown prop '${key}'`);
    	});

    	function select_change_handler() {
    		field.value = select_value(this);
    		$$invalidate(0, field);
    	}

    	$$self.$$set = $$props => {
    		if ("field" in $$props) $$invalidate(0, field = $$props.field);
    	};

    	$$self.$capture_state = () => ({
    		createEventDispatcher,
    		Errors,
    		field,
    		dispatch,
    		setValue,
    		errors
    	});

    	$$self.$inject_state = $$props => {
    		if ("field" in $$props) $$invalidate(0, field = $$props.field);
    		if ("errors" in $$props) $$invalidate(1, errors = $$props.errors);
    	};

    	if ($$props && "$$inject" in $$props) {
    		$$self.$inject_state($$props.$$inject);
    	}

    	$$self.$$.update = () => {
    		if ($$self.$$.dirty & /*field*/ 1) {
    			$$invalidate(1, errors = field.getValidationErors());
    		}
    	};

    	return [field, errors, setValue, select_change_handler];
    }

    class SelectChoice extends SvelteComponentDev {
    	constructor(options) {
    		super(options);
    		init(this, options, instance$4, create_fragment$4, safe_not_equal, { field: 0 });

    		dispatch_dev("SvelteRegisterComponent", {
    			component: this,
    			tagName: "SelectChoice",
    			options,
    			id: create_fragment$4.name
    		});

    		const { ctx } = this.$$;
    		const props = options.props || {};

    		if (/*field*/ ctx[0] === undefined && !("field" in props)) {
    			console.warn("<SelectChoice> was created without expected prop 'field'");
    		}
    	}

    	get field() {
    		throw new Error("<SelectChoice>: Props cannot be read directly from the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}

    	set field(value) {
    		throw new Error("<SelectChoice>: Props cannot be set directly on the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}
    }

    /* src/frontend/Components/Fields/RadioChoice.svelte generated by Svelte v3.32.3 */
    const file$5 = "src/frontend/Components/Fields/RadioChoice.svelte";

    function get_each_context$2(ctx, list, i) {
    	const child_ctx = ctx.slice();
    	child_ctx[6] = list[i];
    	return child_ctx;
    }

    // (15:0) {#if field.label !== undefined}
    function create_if_block$2(ctx) {
    	let legend;
    	let t_value = /*field*/ ctx[0].label + "";
    	let t;

    	const block = {
    		c: function create() {
    			legend = element("legend");
    			t = text(t_value);
    			add_location(legend, file$5, 15, 4, 425);
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, legend, anchor);
    			append_dev(legend, t);
    		},
    		p: function update(ctx, dirty) {
    			if (dirty & /*field*/ 1 && t_value !== (t_value = /*field*/ ctx[0].label + "")) set_data_dev(t, t_value);
    		},
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(legend);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_if_block$2.name,
    		type: "if",
    		source: "(15:0) {#if field.label !== undefined}",
    		ctx
    	});

    	return block;
    }

    // (20:4) {#each field.choices as choice}
    function create_each_block$2(ctx) {
    	let label;
    	let input;
    	let input_value_value;
    	let t0;
    	let t1_value = /*choice*/ ctx[6].label + "";
    	let t1;
    	let mounted;
    	let dispose;

    	const block = {
    		c: function create() {
    			label = element("label");
    			input = element("input");
    			t0 = space();
    			t1 = text(t1_value);
    			attr_dev(input, "type", "radio");
    			input.__value = input_value_value = /*choice*/ ctx[6].value;
    			input.value = input.__value;
    			/*$$binding_groups*/ ctx[4][0].push(input);
    			add_location(input, file$5, 21, 12, 579);
    			attr_dev(label, "class", "svelte-1251773");
    			add_location(label, file$5, 20, 8, 559);
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, label, anchor);
    			append_dev(label, input);
    			input.checked = input.__value === /*field*/ ctx[0].value;
    			append_dev(label, t0);
    			append_dev(label, t1);

    			if (!mounted) {
    				dispose = [
    					listen_dev(input, "change", /*input_change_handler*/ ctx[3]),
    					listen_dev(input, "change", /*setValue*/ ctx[2], false, false, false)
    				];

    				mounted = true;
    			}
    		},
    		p: function update(ctx, dirty) {
    			if (dirty & /*field*/ 1 && input_value_value !== (input_value_value = /*choice*/ ctx[6].value)) {
    				prop_dev(input, "__value", input_value_value);
    				input.value = input.__value;
    			}

    			if (dirty & /*field*/ 1) {
    				input.checked = input.__value === /*field*/ ctx[0].value;
    			}

    			if (dirty & /*field*/ 1 && t1_value !== (t1_value = /*choice*/ ctx[6].label + "")) set_data_dev(t1, t1_value);
    		},
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(label);
    			/*$$binding_groups*/ ctx[4][0].splice(/*$$binding_groups*/ ctx[4][0].indexOf(input), 1);
    			mounted = false;
    			run_all(dispose);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_each_block$2.name,
    		type: "each",
    		source: "(20:4) {#each field.choices as choice}",
    		ctx
    	});

    	return block;
    }

    function create_fragment$5(ctx) {
    	let t0;
    	let section;
    	let t1;
    	let errors_1;
    	let section_class_value;
    	let current;
    	let if_block = /*field*/ ctx[0].label !== undefined && create_if_block$2(ctx);
    	let each_value = /*field*/ ctx[0].choices;
    	validate_each_argument(each_value);
    	let each_blocks = [];

    	for (let i = 0; i < each_value.length; i += 1) {
    		each_blocks[i] = create_each_block$2(get_each_context$2(ctx, each_value, i));
    	}

    	errors_1 = new Errors({
    			props: { errors: /*errors*/ ctx[1] },
    			$$inline: true
    		});

    	const block = {
    		c: function create() {
    			if (if_block) if_block.c();
    			t0 = space();
    			section = element("section");

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].c();
    			}

    			t1 = space();
    			create_component(errors_1.$$.fragment);
    			attr_dev(section, "class", section_class_value = "radio-choice " + /*field*/ ctx[0].getClasses());
    			add_location(section, file$5, 18, 0, 463);
    		},
    		l: function claim(nodes) {
    			throw new Error("options.hydrate only works if the component was compiled with the `hydratable: true` option");
    		},
    		m: function mount(target, anchor) {
    			if (if_block) if_block.m(target, anchor);
    			insert_dev(target, t0, anchor);
    			insert_dev(target, section, anchor);

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].m(section, null);
    			}

    			append_dev(section, t1);
    			mount_component(errors_1, section, null);
    			current = true;
    		},
    		p: function update(ctx, [dirty]) {
    			if (/*field*/ ctx[0].label !== undefined) {
    				if (if_block) {
    					if_block.p(ctx, dirty);
    				} else {
    					if_block = create_if_block$2(ctx);
    					if_block.c();
    					if_block.m(t0.parentNode, t0);
    				}
    			} else if (if_block) {
    				if_block.d(1);
    				if_block = null;
    			}

    			if (dirty & /*field, setValue*/ 5) {
    				each_value = /*field*/ ctx[0].choices;
    				validate_each_argument(each_value);
    				let i;

    				for (i = 0; i < each_value.length; i += 1) {
    					const child_ctx = get_each_context$2(ctx, each_value, i);

    					if (each_blocks[i]) {
    						each_blocks[i].p(child_ctx, dirty);
    					} else {
    						each_blocks[i] = create_each_block$2(child_ctx);
    						each_blocks[i].c();
    						each_blocks[i].m(section, t1);
    					}
    				}

    				for (; i < each_blocks.length; i += 1) {
    					each_blocks[i].d(1);
    				}

    				each_blocks.length = each_value.length;
    			}

    			const errors_1_changes = {};
    			if (dirty & /*errors*/ 2) errors_1_changes.errors = /*errors*/ ctx[1];
    			errors_1.$set(errors_1_changes);

    			if (!current || dirty & /*field*/ 1 && section_class_value !== (section_class_value = "radio-choice " + /*field*/ ctx[0].getClasses())) {
    				attr_dev(section, "class", section_class_value);
    			}
    		},
    		i: function intro(local) {
    			if (current) return;
    			transition_in(errors_1.$$.fragment, local);
    			current = true;
    		},
    		o: function outro(local) {
    			transition_out(errors_1.$$.fragment, local);
    			current = false;
    		},
    		d: function destroy(detaching) {
    			if (if_block) if_block.d(detaching);
    			if (detaching) detach_dev(t0);
    			if (detaching) detach_dev(section);
    			destroy_each(each_blocks, detaching);
    			destroy_component(errors_1);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_fragment$5.name,
    		type: "component",
    		source: "",
    		ctx
    	});

    	return block;
    }

    function instance$5($$self, $$props, $$invalidate) {
    	let errors;
    	let { $$slots: slots = {}, $$scope } = $$props;
    	validate_slots("RadioChoice", slots, []);
    	
    	let { field } = $$props;
    	let dispatch = createEventDispatcher();

    	const setValue = () => {
    		dispatch("update", field.fieldset.form);

    		if (field.params.setNextFieldset) {
    			field.fieldset.form.navigation.nextFieldset();
    		}
    	};

    	const writable_props = ["field"];

    	Object.keys($$props).forEach(key => {
    		if (!~writable_props.indexOf(key) && key.slice(0, 2) !== "$$") console.warn(`<RadioChoice> was created with unknown prop '${key}'`);
    	});

    	const $$binding_groups = [[]];

    	function input_change_handler() {
    		field.value = this.__value;
    		$$invalidate(0, field);
    	}

    	$$self.$$set = $$props => {
    		if ("field" in $$props) $$invalidate(0, field = $$props.field);
    	};

    	$$self.$capture_state = () => ({
    		createEventDispatcher,
    		Errors,
    		field,
    		dispatch,
    		setValue,
    		errors
    	});

    	$$self.$inject_state = $$props => {
    		if ("field" in $$props) $$invalidate(0, field = $$props.field);
    		if ("dispatch" in $$props) dispatch = $$props.dispatch;
    		if ("errors" in $$props) $$invalidate(1, errors = $$props.errors);
    	};

    	if ($$props && "$$inject" in $$props) {
    		$$self.$inject_state($$props.$$inject);
    	}

    	$$self.$$.update = () => {
    		if ($$self.$$.dirty & /*field*/ 1) {
    			$$invalidate(1, errors = field.getValidationErors());
    		}
    	};

    	return [field, errors, setValue, input_change_handler, $$binding_groups];
    }

    class RadioChoice extends SvelteComponentDev {
    	constructor(options) {
    		super(options);
    		init(this, options, instance$5, create_fragment$5, safe_not_equal, { field: 0 });

    		dispatch_dev("SvelteRegisterComponent", {
    			component: this,
    			tagName: "RadioChoice",
    			options,
    			id: create_fragment$5.name
    		});

    		const { ctx } = this.$$;
    		const props = options.props || {};

    		if (/*field*/ ctx[0] === undefined && !("field" in props)) {
    			console.warn("<RadioChoice> was created without expected prop 'field'");
    		}
    	}

    	get field() {
    		throw new Error("<RadioChoice>: Props cannot be read directly from the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}

    	set field(value) {
    		throw new Error("<RadioChoice>: Props cannot be set directly on the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}
    }

    /* src/frontend/Components/Fields/ImageChoice.svelte generated by Svelte v3.32.3 */
    const file$6 = "src/frontend/Components/Fields/ImageChoice.svelte";

    function get_each_context$3(ctx, list, i) {
    	const child_ctx = ctx.slice();
    	child_ctx[6] = list[i];
    	return child_ctx;
    }

    // (15:0) {#if field.label !== undefined}
    function create_if_block$3(ctx) {
    	let legend;
    	let t_value = /*field*/ ctx[0].label + "";
    	let t;

    	const block = {
    		c: function create() {
    			legend = element("legend");
    			t = text(t_value);
    			add_location(legend, file$6, 15, 4, 425);
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, legend, anchor);
    			append_dev(legend, t);
    		},
    		p: function update(ctx, dirty) {
    			if (dirty & /*field*/ 1 && t_value !== (t_value = /*field*/ ctx[0].label + "")) set_data_dev(t, t_value);
    		},
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(legend);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_if_block$3.name,
    		type: "if",
    		source: "(15:0) {#if field.label !== undefined}",
    		ctx
    	});

    	return block;
    }

    // (20:4) {#each field.choices as choice}
    function create_each_block$3(ctx) {
    	let label;
    	let img;
    	let img_src_value;
    	let img_alt_value;
    	let t0;
    	let input;
    	let input_value_value;
    	let t1;
    	let div;
    	let t2_value = /*choice*/ ctx[6].label + "";
    	let t2;
    	let t3;
    	let label_class_value;
    	let mounted;
    	let dispose;

    	const block = {
    		c: function create() {
    			label = element("label");
    			img = element("img");
    			t0 = space();
    			input = element("input");
    			t1 = space();
    			div = element("div");
    			t2 = text(t2_value);
    			t3 = space();
    			if (img.src !== (img_src_value = /*choice*/ ctx[6].image)) attr_dev(img, "src", img_src_value);
    			attr_dev(img, "alt", img_alt_value = /*choice*/ ctx[6].label);
    			add_location(img, file$6, 21, 12, 635);
    			attr_dev(input, "type", "radio");
    			input.__value = input_value_value = /*choice*/ ctx[6].value;
    			input.value = input.__value;
    			attr_dev(input, "class", "svelte-wryltr");
    			/*$$binding_groups*/ ctx[4][0].push(input);
    			add_location(input, file$6, 22, 12, 693);
    			attr_dev(div, "class", "image-text");
    			add_location(div, file$6, 23, 12, 793);

    			attr_dev(label, "class", label_class_value = "" + (null_to_empty(/*choice*/ ctx[6].value === /*field*/ ctx[0].value
    			? "selected"
    			: "") + " svelte-wryltr"));

    			add_location(label, file$6, 20, 8, 559);
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, label, anchor);
    			append_dev(label, img);
    			append_dev(label, t0);
    			append_dev(label, input);
    			input.checked = input.__value === /*field*/ ctx[0].value;
    			append_dev(label, t1);
    			append_dev(label, div);
    			append_dev(div, t2);
    			append_dev(label, t3);

    			if (!mounted) {
    				dispose = [
    					listen_dev(input, "change", /*input_change_handler*/ ctx[3]),
    					listen_dev(input, "change", /*setValue*/ ctx[2], false, false, false)
    				];

    				mounted = true;
    			}
    		},
    		p: function update(ctx, dirty) {
    			if (dirty & /*field*/ 1 && img.src !== (img_src_value = /*choice*/ ctx[6].image)) {
    				attr_dev(img, "src", img_src_value);
    			}

    			if (dirty & /*field*/ 1 && img_alt_value !== (img_alt_value = /*choice*/ ctx[6].label)) {
    				attr_dev(img, "alt", img_alt_value);
    			}

    			if (dirty & /*field*/ 1 && input_value_value !== (input_value_value = /*choice*/ ctx[6].value)) {
    				prop_dev(input, "__value", input_value_value);
    				input.value = input.__value;
    			}

    			if (dirty & /*field*/ 1) {
    				input.checked = input.__value === /*field*/ ctx[0].value;
    			}

    			if (dirty & /*field*/ 1 && t2_value !== (t2_value = /*choice*/ ctx[6].label + "")) set_data_dev(t2, t2_value);

    			if (dirty & /*field*/ 1 && label_class_value !== (label_class_value = "" + (null_to_empty(/*choice*/ ctx[6].value === /*field*/ ctx[0].value
    			? "selected"
    			: "") + " svelte-wryltr"))) {
    				attr_dev(label, "class", label_class_value);
    			}
    		},
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(label);
    			/*$$binding_groups*/ ctx[4][0].splice(/*$$binding_groups*/ ctx[4][0].indexOf(input), 1);
    			mounted = false;
    			run_all(dispose);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_each_block$3.name,
    		type: "each",
    		source: "(20:4) {#each field.choices as choice}",
    		ctx
    	});

    	return block;
    }

    function create_fragment$6(ctx) {
    	let t0;
    	let section;
    	let section_class_value;
    	let t1;
    	let errors_1;
    	let current;
    	let if_block = /*field*/ ctx[0].label !== undefined && create_if_block$3(ctx);
    	let each_value = /*field*/ ctx[0].choices;
    	validate_each_argument(each_value);
    	let each_blocks = [];

    	for (let i = 0; i < each_value.length; i += 1) {
    		each_blocks[i] = create_each_block$3(get_each_context$3(ctx, each_value, i));
    	}

    	errors_1 = new Errors({
    			props: { errors: /*errors*/ ctx[1] },
    			$$inline: true
    		});

    	const block = {
    		c: function create() {
    			if (if_block) if_block.c();
    			t0 = space();
    			section = element("section");

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].c();
    			}

    			t1 = space();
    			create_component(errors_1.$$.fragment);
    			attr_dev(section, "class", section_class_value = "image-choice " + /*field*/ ctx[0].getClasses());
    			add_location(section, file$6, 18, 0, 463);
    		},
    		l: function claim(nodes) {
    			throw new Error("options.hydrate only works if the component was compiled with the `hydratable: true` option");
    		},
    		m: function mount(target, anchor) {
    			if (if_block) if_block.m(target, anchor);
    			insert_dev(target, t0, anchor);
    			insert_dev(target, section, anchor);

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].m(section, null);
    			}

    			insert_dev(target, t1, anchor);
    			mount_component(errors_1, target, anchor);
    			current = true;
    		},
    		p: function update(ctx, [dirty]) {
    			if (/*field*/ ctx[0].label !== undefined) {
    				if (if_block) {
    					if_block.p(ctx, dirty);
    				} else {
    					if_block = create_if_block$3(ctx);
    					if_block.c();
    					if_block.m(t0.parentNode, t0);
    				}
    			} else if (if_block) {
    				if_block.d(1);
    				if_block = null;
    			}

    			if (dirty & /*field, setValue*/ 5) {
    				each_value = /*field*/ ctx[0].choices;
    				validate_each_argument(each_value);
    				let i;

    				for (i = 0; i < each_value.length; i += 1) {
    					const child_ctx = get_each_context$3(ctx, each_value, i);

    					if (each_blocks[i]) {
    						each_blocks[i].p(child_ctx, dirty);
    					} else {
    						each_blocks[i] = create_each_block$3(child_ctx);
    						each_blocks[i].c();
    						each_blocks[i].m(section, null);
    					}
    				}

    				for (; i < each_blocks.length; i += 1) {
    					each_blocks[i].d(1);
    				}

    				each_blocks.length = each_value.length;
    			}

    			if (!current || dirty & /*field*/ 1 && section_class_value !== (section_class_value = "image-choice " + /*field*/ ctx[0].getClasses())) {
    				attr_dev(section, "class", section_class_value);
    			}

    			const errors_1_changes = {};
    			if (dirty & /*errors*/ 2) errors_1_changes.errors = /*errors*/ ctx[1];
    			errors_1.$set(errors_1_changes);
    		},
    		i: function intro(local) {
    			if (current) return;
    			transition_in(errors_1.$$.fragment, local);
    			current = true;
    		},
    		o: function outro(local) {
    			transition_out(errors_1.$$.fragment, local);
    			current = false;
    		},
    		d: function destroy(detaching) {
    			if (if_block) if_block.d(detaching);
    			if (detaching) detach_dev(t0);
    			if (detaching) detach_dev(section);
    			destroy_each(each_blocks, detaching);
    			if (detaching) detach_dev(t1);
    			destroy_component(errors_1, detaching);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_fragment$6.name,
    		type: "component",
    		source: "",
    		ctx
    	});

    	return block;
    }

    function instance$6($$self, $$props, $$invalidate) {
    	let errors;
    	let { $$slots: slots = {}, $$scope } = $$props;
    	validate_slots("ImageChoice", slots, []);
    	
    	let { field } = $$props;
    	let dispatch = createEventDispatcher();

    	const setValue = () => {
    		dispatch("update", field.fieldset.form);

    		if (field.params.setNextFieldset) {
    			field.fieldset.form.navigation.nextFieldset();
    		}
    	};

    	const writable_props = ["field"];

    	Object.keys($$props).forEach(key => {
    		if (!~writable_props.indexOf(key) && key.slice(0, 2) !== "$$") console.warn(`<ImageChoice> was created with unknown prop '${key}'`);
    	});

    	const $$binding_groups = [[]];

    	function input_change_handler() {
    		field.value = this.__value;
    		$$invalidate(0, field);
    	}

    	$$self.$$set = $$props => {
    		if ("field" in $$props) $$invalidate(0, field = $$props.field);
    	};

    	$$self.$capture_state = () => ({
    		createEventDispatcher,
    		Errors,
    		field,
    		dispatch,
    		setValue,
    		errors
    	});

    	$$self.$inject_state = $$props => {
    		if ("field" in $$props) $$invalidate(0, field = $$props.field);
    		if ("dispatch" in $$props) dispatch = $$props.dispatch;
    		if ("errors" in $$props) $$invalidate(1, errors = $$props.errors);
    	};

    	if ($$props && "$$inject" in $$props) {
    		$$self.$inject_state($$props.$$inject);
    	}

    	$$self.$$.update = () => {
    		if ($$self.$$.dirty & /*field*/ 1) {
    			$$invalidate(1, errors = field.getValidationErors());
    		}
    	};

    	return [field, errors, setValue, input_change_handler, $$binding_groups];
    }

    class ImageChoice extends SvelteComponentDev {
    	constructor(options) {
    		super(options);
    		init(this, options, instance$6, create_fragment$6, safe_not_equal, { field: 0 });

    		dispatch_dev("SvelteRegisterComponent", {
    			component: this,
    			tagName: "ImageChoice",
    			options,
    			id: create_fragment$6.name
    		});

    		const { ctx } = this.$$;
    		const props = options.props || {};

    		if (/*field*/ ctx[0] === undefined && !("field" in props)) {
    			console.warn("<ImageChoice> was created without expected prop 'field'");
    		}
    	}

    	get field() {
    		throw new Error("<ImageChoice>: Props cannot be read directly from the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}

    	set field(value) {
    		throw new Error("<ImageChoice>: Props cannot be set directly on the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}
    }

    const subscriber_queue = [];
    /**
     * Create a `Writable` store that allows both updating and reading by subscription.
     * @param {*=}value initial value
     * @param {StartStopNotifier=}start start and stop notifications for subscriptions
     */
    function writable(value, start = noop) {
        let stop;
        const subscribers = [];
        function set(new_value) {
            if (safe_not_equal(value, new_value)) {
                value = new_value;
                if (stop) { // store is ready
                    const run_queue = !subscriber_queue.length;
                    for (let i = 0; i < subscribers.length; i += 1) {
                        const s = subscribers[i];
                        s[1]();
                        subscriber_queue.push(s, value);
                    }
                    if (run_queue) {
                        for (let i = 0; i < subscriber_queue.length; i += 2) {
                            subscriber_queue[i][0](subscriber_queue[i + 1]);
                        }
                        subscriber_queue.length = 0;
                    }
                }
            }
        }
        function update(fn) {
            set(fn(value));
        }
        function subscribe(run, invalidate = noop) {
            const subscriber = [run, invalidate];
            subscribers.push(subscriber);
            if (subscribers.length === 1) {
                stop = start(set) || noop;
            }
            run(value);
            return () => {
                const index = subscribers.indexOf(subscriber);
                if (index !== -1) {
                    subscribers.splice(index, 1);
                }
                if (subscribers.length === 0) {
                    stop();
                    stop = null;
                }
            };
        }
        return { set, update, subscribe };
    }

    function is_date(obj) {
        return Object.prototype.toString.call(obj) === '[object Date]';
    }

    function get_interpolator(a, b) {
        if (a === b || a !== a)
            return () => a;
        const type = typeof a;
        if (type !== typeof b || Array.isArray(a) !== Array.isArray(b)) {
            throw new Error('Cannot interpolate values of different type');
        }
        if (Array.isArray(a)) {
            const arr = b.map((bi, i) => {
                return get_interpolator(a[i], bi);
            });
            return t => arr.map(fn => fn(t));
        }
        if (type === 'object') {
            if (!a || !b)
                throw new Error('Object cannot be null');
            if (is_date(a) && is_date(b)) {
                a = a.getTime();
                b = b.getTime();
                const delta = b - a;
                return t => new Date(a + t * delta);
            }
            const keys = Object.keys(b);
            const interpolators = {};
            keys.forEach(key => {
                interpolators[key] = get_interpolator(a[key], b[key]);
            });
            return t => {
                const result = {};
                keys.forEach(key => {
                    result[key] = interpolators[key](t);
                });
                return result;
            };
        }
        if (type === 'number') {
            const delta = b - a;
            return t => a + t * delta;
        }
        throw new Error(`Cannot interpolate ${type} values`);
    }
    function tweened(value, defaults = {}) {
        const store = writable(value);
        let task;
        let target_value = value;
        function set(new_value, opts) {
            if (value == null) {
                store.set(value = new_value);
                return Promise.resolve();
            }
            target_value = new_value;
            let previous_task = task;
            let started = false;
            let { delay = 0, duration = 400, easing = identity, interpolate = get_interpolator } = assign(assign({}, defaults), opts);
            if (duration === 0) {
                if (previous_task) {
                    previous_task.abort();
                    previous_task = null;
                }
                store.set(value = target_value);
                return Promise.resolve();
            }
            const start = now() + delay;
            let fn;
            task = loop(now => {
                if (now < start)
                    return true;
                if (!started) {
                    fn = interpolate(value, new_value);
                    if (typeof duration === 'function')
                        duration = duration(value, new_value);
                    started = true;
                }
                if (previous_task) {
                    previous_task.abort();
                    previous_task = null;
                }
                const elapsed = now - start;
                if (elapsed > duration) {
                    store.set(value = new_value);
                    return false;
                }
                // @ts-ignore
                store.set(value = fn(easing(elapsed / duration)));
                return true;
            });
            return task.promise;
        }
        return {
            set,
            update: (fn, opts) => set(fn(target_value, value), opts),
            subscribe: store.subscribe
        };
    }

    /* src/frontend/Components/Percentage.svelte generated by Svelte v3.32.3 */
    const file$7 = "src/frontend/Components/Percentage.svelte";

    // (18:4) {#if text === true}
    function create_if_block$4(ctx) {
    	let div;
    	let t0;
    	let t1;

    	const block = {
    		c: function create() {
    			div = element("div");
    			t0 = text(/*percentage*/ ctx[0]);
    			t1 = text("%");
    			attr_dev(div, "class", "percentage-text svelte-h0i7g2");
    			set_style(div, "height", /*height*/ ctx[2] + ": color " + /*textColor*/ ctx[6]);
    			add_location(div, file$7, 18, 8, 577);
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, div, anchor);
    			append_dev(div, t0);
    			append_dev(div, t1);
    		},
    		p: function update(ctx, dirty) {
    			if (dirty & /*percentage*/ 1) set_data_dev(t0, /*percentage*/ ctx[0]);
    		},
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(div);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_if_block$4.name,
    		type: "if",
    		source: "(18:4) {#if text === true}",
    		ctx
    	});

    	return block;
    }

    function create_fragment$7(ctx) {
    	let div1;
    	let div0;
    	let t;
    	let if_block = /*text*/ ctx[5] === true && create_if_block$4(ctx);

    	const block = {
    		c: function create() {
    			div1 = element("div");
    			div0 = element("div");
    			t = space();
    			if (if_block) if_block.c();
    			attr_dev(div0, "class", "percentage-bar svelte-h0i7g2");
    			set_style(div0, "width", /*$barSize*/ ctx[1] + "%");
    			set_style(div0, "height", /*height*/ ctx[2]);
    			set_style(div0, "background-color", /*color*/ ctx[3]);
    			add_location(div0, file$7, 16, 4, 442);
    			attr_dev(div1, "class", "percentage svelte-h0i7g2");
    			set_style(div1, "height", /*height*/ ctx[2]);
    			set_style(div1, "line-height", /*height*/ ctx[2]);
    			set_style(div1, "background-color", /*bgColor*/ ctx[4]);
    			add_location(div1, file$7, 15, 0, 336);
    		},
    		l: function claim(nodes) {
    			throw new Error("options.hydrate only works if the component was compiled with the `hydratable: true` option");
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, div1, anchor);
    			append_dev(div1, div0);
    			append_dev(div1, t);
    			if (if_block) if_block.m(div1, null);
    		},
    		p: function update(ctx, [dirty]) {
    			if (dirty & /*$barSize*/ 2) {
    				set_style(div0, "width", /*$barSize*/ ctx[1] + "%");
    			}

    			if (/*text*/ ctx[5] === true) if_block.p(ctx, dirty);
    		},
    		i: noop,
    		o: noop,
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(div1);
    			if (if_block) if_block.d();
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_fragment$7.name,
    		type: "component",
    		source: "",
    		ctx
    	});

    	return block;
    }

    function instance$7($$self, $$props, $$invalidate) {
    	let $barSize;
    	let { $$slots: slots = {}, $$scope } = $$props;
    	validate_slots("Percentage", slots, []);
    	let { start } = $$props;
    	let { percentage } = $$props;
    	let height = "5px";
    	let color = "#41a62a";
    	let bgColor = "#f3efe9";
    	let text = false;
    	let textColor = "#FFFFFF";
    	const barSize = tweened(start, { delay: 1000 });
    	validate_store(barSize, "barSize");
    	component_subscribe($$self, barSize, value => $$invalidate(1, $barSize = value));
    	const writable_props = ["start", "percentage"];

    	Object.keys($$props).forEach(key => {
    		if (!~writable_props.indexOf(key) && key.slice(0, 2) !== "$$") console.warn(`<Percentage> was created with unknown prop '${key}'`);
    	});

    	$$self.$$set = $$props => {
    		if ("start" in $$props) $$invalidate(8, start = $$props.start);
    		if ("percentage" in $$props) $$invalidate(0, percentage = $$props.percentage);
    	};

    	$$self.$capture_state = () => ({
    		tweened,
    		start,
    		percentage,
    		height,
    		color,
    		bgColor,
    		text,
    		textColor,
    		barSize,
    		$barSize
    	});

    	$$self.$inject_state = $$props => {
    		if ("start" in $$props) $$invalidate(8, start = $$props.start);
    		if ("percentage" in $$props) $$invalidate(0, percentage = $$props.percentage);
    		if ("height" in $$props) $$invalidate(2, height = $$props.height);
    		if ("color" in $$props) $$invalidate(3, color = $$props.color);
    		if ("bgColor" in $$props) $$invalidate(4, bgColor = $$props.bgColor);
    		if ("text" in $$props) $$invalidate(5, text = $$props.text);
    		if ("textColor" in $$props) $$invalidate(6, textColor = $$props.textColor);
    	};

    	if ($$props && "$$inject" in $$props) {
    		$$self.$inject_state($$props.$$inject);
    	}

    	$$self.$$.update = () => {
    		if ($$self.$$.dirty & /*percentage*/ 1) {
    			barSize.set(percentage);
    		}
    	};

    	return [percentage, $barSize, height, color, bgColor, text, textColor, barSize, start];
    }

    class Percentage extends SvelteComponentDev {
    	constructor(options) {
    		super(options);
    		init(this, options, instance$7, create_fragment$7, safe_not_equal, { start: 8, percentage: 0 });

    		dispatch_dev("SvelteRegisterComponent", {
    			component: this,
    			tagName: "Percentage",
    			options,
    			id: create_fragment$7.name
    		});

    		const { ctx } = this.$$;
    		const props = options.props || {};

    		if (/*start*/ ctx[8] === undefined && !("start" in props)) {
    			console.warn("<Percentage> was created without expected prop 'start'");
    		}

    		if (/*percentage*/ ctx[0] === undefined && !("percentage" in props)) {
    			console.warn("<Percentage> was created without expected prop 'percentage'");
    		}
    	}

    	get start() {
    		throw new Error("<Percentage>: Props cannot be read directly from the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}

    	set start(value) {
    		throw new Error("<Percentage>: Props cannot be set directly on the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}

    	get percentage() {
    		throw new Error("<Percentage>: Props cannot be read directly from the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}

    	set percentage(value) {
    		throw new Error("<Percentage>: Props cannot be set directly on the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}
    }

    /* src/frontend/Components/Fieldset.svelte generated by Svelte v3.32.3 */
    const file$8 = "src/frontend/Components/Fieldset.svelte";

    function get_each_context$4(ctx, list, i) {
    	const child_ctx = ctx.slice();
    	child_ctx[6] = list[i];
    	return child_ctx;
    }

    // (47:51) 
    function create_if_block_5(ctx) {
    	let imagechoice;
    	let current;

    	imagechoice = new ImageChoice({
    			props: { field: /*field*/ ctx[6] },
    			$$inline: true
    		});

    	imagechoice.$on("update", /*update*/ ctx[3]);

    	const block = {
    		c: function create() {
    			create_component(imagechoice.$$.fragment);
    		},
    		m: function mount(target, anchor) {
    			mount_component(imagechoice, target, anchor);
    			current = true;
    		},
    		p: function update(ctx, dirty) {
    			const imagechoice_changes = {};
    			if (dirty & /*fields*/ 4) imagechoice_changes.field = /*field*/ ctx[6];
    			imagechoice.$set(imagechoice_changes);
    		},
    		i: function intro(local) {
    			if (current) return;
    			transition_in(imagechoice.$$.fragment, local);
    			current = true;
    		},
    		o: function outro(local) {
    			transition_out(imagechoice.$$.fragment, local);
    			current = false;
    		},
    		d: function destroy(detaching) {
    			destroy_component(imagechoice, detaching);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_if_block_5.name,
    		type: "if",
    		source: "(47:51) ",
    		ctx
    	});

    	return block;
    }

    // (45:51) 
    function create_if_block_4(ctx) {
    	let radiochoice;
    	let current;

    	radiochoice = new RadioChoice({
    			props: { field: /*field*/ ctx[6] },
    			$$inline: true
    		});

    	radiochoice.$on("update", /*update*/ ctx[3]);

    	const block = {
    		c: function create() {
    			create_component(radiochoice.$$.fragment);
    		},
    		m: function mount(target, anchor) {
    			mount_component(radiochoice, target, anchor);
    			current = true;
    		},
    		p: function update(ctx, dirty) {
    			const radiochoice_changes = {};
    			if (dirty & /*fields*/ 4) radiochoice_changes.field = /*field*/ ctx[6];
    			radiochoice.$set(radiochoice_changes);
    		},
    		i: function intro(local) {
    			if (current) return;
    			transition_in(radiochoice.$$.fragment, local);
    			current = true;
    		},
    		o: function outro(local) {
    			transition_out(radiochoice.$$.fragment, local);
    			current = false;
    		},
    		d: function destroy(detaching) {
    			destroy_component(radiochoice, detaching);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_if_block_4.name,
    		type: "if",
    		source: "(45:51) ",
    		ctx
    	});

    	return block;
    }

    // (43:52) 
    function create_if_block_3(ctx) {
    	let selectchoice;
    	let current;

    	selectchoice = new SelectChoice({
    			props: { field: /*field*/ ctx[6] },
    			$$inline: true
    		});

    	selectchoice.$on("update", /*update*/ ctx[3]);

    	const block = {
    		c: function create() {
    			create_component(selectchoice.$$.fragment);
    		},
    		m: function mount(target, anchor) {
    			mount_component(selectchoice, target, anchor);
    			current = true;
    		},
    		p: function update(ctx, dirty) {
    			const selectchoice_changes = {};
    			if (dirty & /*fields*/ 4) selectchoice_changes.field = /*field*/ ctx[6];
    			selectchoice.$set(selectchoice_changes);
    		},
    		i: function intro(local) {
    			if (current) return;
    			transition_in(selectchoice.$$.fragment, local);
    			current = true;
    		},
    		o: function outro(local) {
    			transition_out(selectchoice.$$.fragment, local);
    			current = false;
    		},
    		d: function destroy(detaching) {
    			destroy_component(selectchoice, detaching);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_if_block_3.name,
    		type: "if",
    		source: "(43:52) ",
    		ctx
    	});

    	return block;
    }

    // (41:45) 
    function create_if_block_2(ctx) {
    	let range;
    	let current;

    	range = new Range({
    			props: { field: /*field*/ ctx[6] },
    			$$inline: true
    		});

    	range.$on("update", /*update*/ ctx[3]);

    	const block = {
    		c: function create() {
    			create_component(range.$$.fragment);
    		},
    		m: function mount(target, anchor) {
    			mount_component(range, target, anchor);
    			current = true;
    		},
    		p: function update(ctx, dirty) {
    			const range_changes = {};
    			if (dirty & /*fields*/ 4) range_changes.field = /*field*/ ctx[6];
    			range.$set(range_changes);
    		},
    		i: function intro(local) {
    			if (current) return;
    			transition_in(range.$$.fragment, local);
    			current = true;
    		},
    		o: function outro(local) {
    			transition_out(range.$$.fragment, local);
    			current = false;
    		},
    		d: function destroy(detaching) {
    			destroy_component(range, detaching);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_if_block_2.name,
    		type: "if",
    		source: "(41:45) ",
    		ctx
    	});

    	return block;
    }

    // (39:48) 
    function create_if_block_1(ctx) {
    	let textarea;
    	let current;

    	textarea = new Textarea({
    			props: { field: /*field*/ ctx[6] },
    			$$inline: true
    		});

    	textarea.$on("update", /*update*/ ctx[3]);

    	const block = {
    		c: function create() {
    			create_component(textarea.$$.fragment);
    		},
    		m: function mount(target, anchor) {
    			mount_component(textarea, target, anchor);
    			current = true;
    		},
    		p: function update(ctx, dirty) {
    			const textarea_changes = {};
    			if (dirty & /*fields*/ 4) textarea_changes.field = /*field*/ ctx[6];
    			textarea.$set(textarea_changes);
    		},
    		i: function intro(local) {
    			if (current) return;
    			transition_in(textarea.$$.fragment, local);
    			current = true;
    		},
    		o: function outro(local) {
    			transition_out(textarea.$$.fragment, local);
    			current = false;
    		},
    		d: function destroy(detaching) {
    			destroy_component(textarea, detaching);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_if_block_1.name,
    		type: "if",
    		source: "(39:48) ",
    		ctx
    	});

    	return block;
    }

    // (37:12) {#if field.type === 'Text'}
    function create_if_block$5(ctx) {
    	let text_1;
    	let current;

    	text_1 = new Text({
    			props: { field: /*field*/ ctx[6] },
    			$$inline: true
    		});

    	text_1.$on("update", /*update*/ ctx[3]);

    	const block = {
    		c: function create() {
    			create_component(text_1.$$.fragment);
    		},
    		m: function mount(target, anchor) {
    			mount_component(text_1, target, anchor);
    			current = true;
    		},
    		p: function update(ctx, dirty) {
    			const text_1_changes = {};
    			if (dirty & /*fields*/ 4) text_1_changes.field = /*field*/ ctx[6];
    			text_1.$set(text_1_changes);
    		},
    		i: function intro(local) {
    			if (current) return;
    			transition_in(text_1.$$.fragment, local);
    			current = true;
    		},
    		o: function outro(local) {
    			transition_out(text_1.$$.fragment, local);
    			current = false;
    		},
    		d: function destroy(detaching) {
    			destroy_component(text_1, detaching);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_if_block$5.name,
    		type: "if",
    		source: "(37:12) {#if field.type === 'Text'}",
    		ctx
    	});

    	return block;
    }

    // (36:8) {#each fields as field}
    function create_each_block$4(ctx) {
    	let current_block_type_index;
    	let if_block;
    	let if_block_anchor;
    	let current;

    	const if_block_creators = [
    		create_if_block$5,
    		create_if_block_1,
    		create_if_block_2,
    		create_if_block_3,
    		create_if_block_4,
    		create_if_block_5
    	];

    	const if_blocks = [];

    	function select_block_type(ctx, dirty) {
    		if (/*field*/ ctx[6].type === "Text") return 0;
    		if (/*field*/ ctx[6].type === "TextArea") return 1;
    		if (/*field*/ ctx[6].type === "Range") return 2;
    		if (/*field*/ ctx[6].type === "SelectChoice") return 3;
    		if (/*field*/ ctx[6].type === "RadioChoice") return 4;
    		if (/*field*/ ctx[6].type === "ImageChoice") return 5;
    		return -1;
    	}

    	if (~(current_block_type_index = select_block_type(ctx))) {
    		if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);
    	}

    	const block = {
    		c: function create() {
    			if (if_block) if_block.c();
    			if_block_anchor = empty();
    		},
    		m: function mount(target, anchor) {
    			if (~current_block_type_index) {
    				if_blocks[current_block_type_index].m(target, anchor);
    			}

    			insert_dev(target, if_block_anchor, anchor);
    			current = true;
    		},
    		p: function update(ctx, dirty) {
    			let previous_block_index = current_block_type_index;
    			current_block_type_index = select_block_type(ctx);

    			if (current_block_type_index === previous_block_index) {
    				if (~current_block_type_index) {
    					if_blocks[current_block_type_index].p(ctx, dirty);
    				}
    			} else {
    				if (if_block) {
    					group_outros();

    					transition_out(if_blocks[previous_block_index], 1, 1, () => {
    						if_blocks[previous_block_index] = null;
    					});

    					check_outros();
    				}

    				if (~current_block_type_index) {
    					if_block = if_blocks[current_block_type_index];

    					if (!if_block) {
    						if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);
    						if_block.c();
    					} else {
    						if_block.p(ctx, dirty);
    					}

    					transition_in(if_block, 1);
    					if_block.m(if_block_anchor.parentNode, if_block_anchor);
    				} else {
    					if_block = null;
    				}
    			}
    		},
    		i: function intro(local) {
    			if (current) return;
    			transition_in(if_block);
    			current = true;
    		},
    		o: function outro(local) {
    			transition_out(if_block);
    			current = false;
    		},
    		d: function destroy(detaching) {
    			if (~current_block_type_index) {
    				if_blocks[current_block_type_index].d(detaching);
    			}

    			if (detaching) detach_dev(if_block_anchor);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_each_block$4.name,
    		type: "each",
    		source: "(36:8) {#each fields as field}",
    		ctx
    	});

    	return block;
    }

    function create_fragment$8(ctx) {
    	let fieldset_1;
    	let legend;
    	let t0_value = /*fieldset*/ ctx[0].label + "";
    	let t0;
    	let t1;
    	let percentage;
    	let t2;
    	let div;
    	let div_class_value;
    	let div_intro;
    	let div_outro;
    	let fieldset_1_class_value;
    	let current;

    	percentage = new Percentage({
    			props: {
    				start: /*percentageStart*/ ctx[1],
    				percentage: /*percentageCurrent*/ ctx[4]
    			},
    			$$inline: true
    		});

    	let each_value = /*fields*/ ctx[2];
    	validate_each_argument(each_value);
    	let each_blocks = [];

    	for (let i = 0; i < each_value.length; i += 1) {
    		each_blocks[i] = create_each_block$4(get_each_context$4(ctx, each_value, i));
    	}

    	const out = i => transition_out(each_blocks[i], 1, 1, () => {
    		each_blocks[i] = null;
    	});

    	const block = {
    		c: function create() {
    			fieldset_1 = element("fieldset");
    			legend = element("legend");
    			t0 = text(t0_value);
    			t1 = space();
    			create_component(percentage.$$.fragment);
    			t2 = space();
    			div = element("div");

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].c();
    			}

    			add_location(legend, file$8, 32, 4, 1107);
    			attr_dev(div, "class", div_class_value = "fields " + /*fieldset*/ ctx[0].getFieldsClasses());
    			add_location(div, file$8, 34, 4, 1219);
    			attr_dev(fieldset_1, "class", fieldset_1_class_value = "" + (null_to_empty(/*fieldset*/ ctx[0].getClasses()) + " svelte-ovh2fw"));
    			add_location(fieldset_1, file$8, 31, 0, 1062);
    		},
    		l: function claim(nodes) {
    			throw new Error("options.hydrate only works if the component was compiled with the `hydratable: true` option");
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, fieldset_1, anchor);
    			append_dev(fieldset_1, legend);
    			append_dev(legend, t0);
    			append_dev(fieldset_1, t1);
    			mount_component(percentage, fieldset_1, null);
    			append_dev(fieldset_1, t2);
    			append_dev(fieldset_1, div);

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].m(div, null);
    			}

    			current = true;
    		},
    		p: function update(ctx, [dirty]) {
    			if ((!current || dirty & /*fieldset*/ 1) && t0_value !== (t0_value = /*fieldset*/ ctx[0].label + "")) set_data_dev(t0, t0_value);
    			const percentage_changes = {};
    			if (dirty & /*percentageStart*/ 2) percentage_changes.start = /*percentageStart*/ ctx[1];
    			percentage.$set(percentage_changes);

    			if (dirty & /*fields, update*/ 12) {
    				each_value = /*fields*/ ctx[2];
    				validate_each_argument(each_value);
    				let i;

    				for (i = 0; i < each_value.length; i += 1) {
    					const child_ctx = get_each_context$4(ctx, each_value, i);

    					if (each_blocks[i]) {
    						each_blocks[i].p(child_ctx, dirty);
    						transition_in(each_blocks[i], 1);
    					} else {
    						each_blocks[i] = create_each_block$4(child_ctx);
    						each_blocks[i].c();
    						transition_in(each_blocks[i], 1);
    						each_blocks[i].m(div, null);
    					}
    				}

    				group_outros();

    				for (i = each_value.length; i < each_blocks.length; i += 1) {
    					out(i);
    				}

    				check_outros();
    			}

    			if (!current || dirty & /*fieldset*/ 1 && div_class_value !== (div_class_value = "fields " + /*fieldset*/ ctx[0].getFieldsClasses())) {
    				attr_dev(div, "class", div_class_value);
    			}

    			if (!current || dirty & /*fieldset*/ 1 && fieldset_1_class_value !== (fieldset_1_class_value = "" + (null_to_empty(/*fieldset*/ ctx[0].getClasses()) + " svelte-ovh2fw"))) {
    				attr_dev(fieldset_1, "class", fieldset_1_class_value);
    			}
    		},
    		i: function intro(local) {
    			if (current) return;
    			transition_in(percentage.$$.fragment, local);

    			for (let i = 0; i < each_value.length; i += 1) {
    				transition_in(each_blocks[i]);
    			}

    			add_render_callback(() => {
    				if (div_outro) div_outro.end(1);
    				if (!div_intro) div_intro = create_in_transition(div, fade, { duration: 500, delay: 500 });
    				div_intro.start();
    			});

    			current = true;
    		},
    		o: function outro(local) {
    			transition_out(percentage.$$.fragment, local);
    			each_blocks = each_blocks.filter(Boolean);

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				transition_out(each_blocks[i]);
    			}

    			if (div_intro) div_intro.invalidate();
    			div_outro = create_out_transition(div, fade, { duration: 500 });
    			current = false;
    		},
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(fieldset_1);
    			destroy_component(percentage);
    			destroy_each(each_blocks, detaching);
    			if (detaching && div_outro) div_outro.end();
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_fragment$8.name,
    		type: "component",
    		source: "",
    		ctx
    	});

    	return block;
    }

    function instance$8($$self, $$props, $$invalidate) {
    	let fields;
    	let { $$slots: slots = {}, $$scope } = $$props;
    	validate_slots("Fieldset", slots, []);
    	
    	let { fieldset } = $$props;
    	const dispatch = createEventDispatcher();

    	function update(form) {
    		dispatch("update", form.detail);
    	}

    	let percentageStart;

    	switch (fieldset.form.navigation.getLastAction()) {
    		case "prev":
    			percentageStart = fieldset.form.navigation.getNextFieldset().percentage;
    			break;
    		case "next":
    			percentageStart = fieldset.form.navigation.getPrevFieldset().percentage;
    			break;
    		default:
    			percentageStart = 0;
    			break;
    	}

    	let percentageCurrent = fieldset.form.navigation.getCurrentFieldset().percentage;
    	const writable_props = ["fieldset"];

    	Object.keys($$props).forEach(key => {
    		if (!~writable_props.indexOf(key) && key.slice(0, 2) !== "$$") console.warn(`<Fieldset> was created with unknown prop '${key}'`);
    	});

    	$$self.$$set = $$props => {
    		if ("fieldset" in $$props) $$invalidate(0, fieldset = $$props.fieldset);
    	};

    	$$self.$capture_state = () => ({
    		createEventDispatcher,
    		fade,
    		Text,
    		Textarea,
    		Range,
    		SelectChoice,
    		RadioChoice,
    		ImageChoice,
    		Percentage,
    		fieldset,
    		dispatch,
    		update,
    		percentageStart,
    		percentageCurrent,
    		fields
    	});

    	$$self.$inject_state = $$props => {
    		if ("fieldset" in $$props) $$invalidate(0, fieldset = $$props.fieldset);
    		if ("percentageStart" in $$props) $$invalidate(1, percentageStart = $$props.percentageStart);
    		if ("percentageCurrent" in $$props) $$invalidate(4, percentageCurrent = $$props.percentageCurrent);
    		if ("fields" in $$props) $$invalidate(2, fields = $$props.fields);
    	};

    	if ($$props && "$$inject" in $$props) {
    		$$self.$inject_state($$props.$$inject);
    	}

    	$$self.$$.update = () => {
    		if ($$self.$$.dirty & /*fieldset*/ 1) {
    			$$invalidate(2, fields = fieldset.fields);
    		}
    	};

    	return [fieldset, percentageStart, fields, update, percentageCurrent];
    }

    class Fieldset$1 extends SvelteComponentDev {
    	constructor(options) {
    		super(options);
    		init(this, options, instance$8, create_fragment$8, safe_not_equal, { fieldset: 0 });

    		dispatch_dev("SvelteRegisterComponent", {
    			component: this,
    			tagName: "Fieldset",
    			options,
    			id: create_fragment$8.name
    		});

    		const { ctx } = this.$$;
    		const props = options.props || {};

    		if (/*fieldset*/ ctx[0] === undefined && !("fieldset" in props)) {
    			console.warn("<Fieldset> was created without expected prop 'fieldset'");
    		}
    	}

    	get fieldset() {
    		throw new Error("<Fieldset>: Props cannot be read directly from the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}

    	set fieldset(value) {
    		throw new Error("<Fieldset>: Props cannot be set directly on the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}
    }

    /* src/frontend/Components/Navigation.svelte generated by Svelte v3.32.3 */
    const file$9 = "src/frontend/Components/Navigation.svelte";

    // (39:4) {#if showPrev}
    function create_if_block_2$1(ctx) {
    	let button;
    	let mounted;
    	let dispose;

    	const block = {
    		c: function create() {
    			button = element("button");
    			button.textContent = "< Zurück";
    			add_location(button, file$9, 39, 8, 1151);
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, button, anchor);

    			if (!mounted) {
    				dispose = listen_dev(button, "click", /*click_handler*/ ctx[6], false, false, false);
    				mounted = true;
    			}
    		},
    		p: noop,
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(button);
    			mounted = false;
    			dispose();
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_if_block_2$1.name,
    		type: "if",
    		source: "(39:4) {#if showPrev}",
    		ctx
    	});

    	return block;
    }

    // (42:4) {#if showNext}
    function create_if_block_1$1(ctx) {
    	let button;
    	let mounted;
    	let dispose;

    	const block = {
    		c: function create() {
    			button = element("button");
    			button.textContent = "Weiter";
    			add_location(button, file$9, 42, 8, 1253);
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, button, anchor);

    			if (!mounted) {
    				dispose = listen_dev(button, "click", /*click_handler_1*/ ctx[7], false, false, false);
    				mounted = true;
    			}
    		},
    		p: noop,
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(button);
    			mounted = false;
    			dispose();
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_if_block_1$1.name,
    		type: "if",
    		source: "(42:4) {#if showNext}",
    		ctx
    	});

    	return block;
    }

    // (45:4) {#if showSend}
    function create_if_block$6(ctx) {
    	let button;
    	let mounted;
    	let dispose;

    	const block = {
    		c: function create() {
    			button = element("button");
    			button.textContent = "Absenden";
    			attr_dev(button, "class", "svelte-1wpmhsr");
    			toggle_class(button, "loading", /*loading*/ ctx[0]);
    			add_location(button, file$9, 45, 8, 1350);
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, button, anchor);

    			if (!mounted) {
    				dispose = listen_dev(button, "click", /*click_handler_2*/ ctx[8], false, false, false);
    				mounted = true;
    			}
    		},
    		p: function update(ctx, dirty) {
    			if (dirty & /*loading*/ 1) {
    				toggle_class(button, "loading", /*loading*/ ctx[0]);
    			}
    		},
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(button);
    			mounted = false;
    			dispose();
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_if_block$6.name,
    		type: "if",
    		source: "(45:4) {#if showSend}",
    		ctx
    	});

    	return block;
    }

    function create_fragment$9(ctx) {
    	let nav;
    	let t0;
    	let t1;
    	let if_block0 = /*showPrev*/ ctx[1] && create_if_block_2$1(ctx);
    	let if_block1 = /*showNext*/ ctx[2] && create_if_block_1$1(ctx);
    	let if_block2 = /*showSend*/ ctx[3] && create_if_block$6(ctx);

    	const block = {
    		c: function create() {
    			nav = element("nav");
    			if (if_block0) if_block0.c();
    			t0 = space();
    			if (if_block1) if_block1.c();
    			t1 = space();
    			if (if_block2) if_block2.c();
    			add_location(nav, file$9, 37, 0, 1118);
    		},
    		l: function claim(nodes) {
    			throw new Error("options.hydrate only works if the component was compiled with the `hydratable: true` option");
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, nav, anchor);
    			if (if_block0) if_block0.m(nav, null);
    			append_dev(nav, t0);
    			if (if_block1) if_block1.m(nav, null);
    			append_dev(nav, t1);
    			if (if_block2) if_block2.m(nav, null);
    		},
    		p: function update(ctx, [dirty]) {
    			if (/*showPrev*/ ctx[1]) {
    				if (if_block0) {
    					if_block0.p(ctx, dirty);
    				} else {
    					if_block0 = create_if_block_2$1(ctx);
    					if_block0.c();
    					if_block0.m(nav, t0);
    				}
    			} else if (if_block0) {
    				if_block0.d(1);
    				if_block0 = null;
    			}

    			if (/*showNext*/ ctx[2]) {
    				if (if_block1) {
    					if_block1.p(ctx, dirty);
    				} else {
    					if_block1 = create_if_block_1$1(ctx);
    					if_block1.c();
    					if_block1.m(nav, t1);
    				}
    			} else if (if_block1) {
    				if_block1.d(1);
    				if_block1 = null;
    			}

    			if (/*showSend*/ ctx[3]) {
    				if (if_block2) {
    					if_block2.p(ctx, dirty);
    				} else {
    					if_block2 = create_if_block$6(ctx);
    					if_block2.c();
    					if_block2.m(nav, null);
    				}
    			} else if (if_block2) {
    				if_block2.d(1);
    				if_block2 = null;
    			}
    		},
    		i: noop,
    		o: noop,
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(nav);
    			if (if_block0) if_block0.d();
    			if (if_block1) if_block1.d();
    			if (if_block2) if_block2.d();
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_fragment$9.name,
    		type: "component",
    		source: "",
    		ctx
    	});

    	return block;
    }

    function instance$9($$self, $$props, $$invalidate) {
    	let showPrev;
    	let showNext;
    	let showSend;
    	let { $$slots: slots = {}, $$scope } = $$props;
    	validate_slots("Navigation", slots, []);
    	
    	const dispatch = createEventDispatcher();
    	let { navigation } = $$props;
    	let loading = false;

    	const navigate = direction => {
    		switch (direction) {
    			case "prev":
    				navigation.prevFieldset();
    				dispatch("navigate", navigation.form);
    				break;
    			case "next":
    				navigation.nextFieldset();
    				dispatch("navigate", navigation.form);
    				break;
    			case "send":
    				$$invalidate(0, loading = true);
    				navigation.submit().then(response => {
    					$$invalidate(0, loading = false);

    					if (response.status === 200) {
    						$$invalidate(5, navigation.form.sent = true, navigation);
    					}

    					dispatch("navigate", navigation.form);
    				}).catch(error => {
    					$$invalidate(0, loading = false);
    					dispatch("navigate", navigation.form);
    				});
    				break;
    		}
    	};

    	const writable_props = ["navigation"];

    	Object.keys($$props).forEach(key => {
    		if (!~writable_props.indexOf(key) && key.slice(0, 2) !== "$$") console.warn(`<Navigation> was created with unknown prop '${key}'`);
    	});

    	const click_handler = () => navigate("prev");
    	const click_handler_1 = () => navigate("next");
    	const click_handler_2 = () => navigate("send");

    	$$self.$$set = $$props => {
    		if ("navigation" in $$props) $$invalidate(5, navigation = $$props.navigation);
    	};

    	$$self.$capture_state = () => ({
    		createEventDispatcher,
    		dispatch,
    		navigation,
    		loading,
    		navigate,
    		showPrev,
    		showNext,
    		showSend
    	});

    	$$self.$inject_state = $$props => {
    		if ("navigation" in $$props) $$invalidate(5, navigation = $$props.navigation);
    		if ("loading" in $$props) $$invalidate(0, loading = $$props.loading);
    		if ("showPrev" in $$props) $$invalidate(1, showPrev = $$props.showPrev);
    		if ("showNext" in $$props) $$invalidate(2, showNext = $$props.showNext);
    		if ("showSend" in $$props) $$invalidate(3, showSend = $$props.showSend);
    	};

    	if ($$props && "$$inject" in $$props) {
    		$$self.$inject_state($$props.$$inject);
    	}

    	$$self.$$.update = () => {
    		if ($$self.$$.dirty & /*navigation*/ 32) {
    			$$invalidate(1, showPrev = navigation.hasPrevFieldset());
    		}

    		if ($$self.$$.dirty & /*navigation*/ 32) {
    			$$invalidate(2, showNext = navigation.hasNextFieldset());
    		}

    		if ($$self.$$.dirty & /*navigation*/ 32) {
    			$$invalidate(3, showSend = navigation.hasSubmission());
    		}
    	};

    	return [
    		loading,
    		showPrev,
    		showNext,
    		showSend,
    		navigate,
    		navigation,
    		click_handler,
    		click_handler_1,
    		click_handler_2
    	];
    }

    class Navigation$1 extends SvelteComponentDev {
    	constructor(options) {
    		super(options);
    		init(this, options, instance$9, create_fragment$9, safe_not_equal, { navigation: 5 });

    		dispatch_dev("SvelteRegisterComponent", {
    			component: this,
    			tagName: "Navigation",
    			options,
    			id: create_fragment$9.name
    		});

    		const { ctx } = this.$$;
    		const props = options.props || {};

    		if (/*navigation*/ ctx[5] === undefined && !("navigation" in props)) {
    			console.warn("<Navigation> was created without expected prop 'navigation'");
    		}
    	}

    	get navigation() {
    		throw new Error("<Navigation>: Props cannot be read directly from the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}

    	set navigation(value) {
    		throw new Error("<Navigation>: Props cannot be set directly on the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}
    }

    /* src/frontend/Components/Footer.svelte generated by Svelte v3.32.3 */

    const file$a = "src/frontend/Components/Footer.svelte";

    function create_fragment$a(ctx) {
    	let div;
    	let ul;
    	let li0;
    	let t1;
    	let li1;
    	let t3;
    	let li2;

    	const block = {
    		c: function create() {
    			div = element("div");
    			ul = element("ul");
    			li0 = element("li");
    			li0.textContent = "Genaue Wertermittlung";
    			t1 = space();
    			li1 = element("li");
    			li1.textContent = "Über 900 Kundenmeinungen";
    			t3 = space();
    			li2 = element("li");
    			li2.textContent = "kostenlos & unverbindlich";
    			add_location(li0, file$a, 2, 8, 47);
    			add_location(li1, file$a, 3, 8, 86);
    			add_location(li2, file$a, 4, 8, 128);
    			add_location(ul, file$a, 1, 4, 34);
    			attr_dev(div, "class", "recommendations");
    			add_location(div, file$a, 0, 0, 0);
    		},
    		l: function claim(nodes) {
    			throw new Error("options.hydrate only works if the component was compiled with the `hydratable: true` option");
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, div, anchor);
    			append_dev(div, ul);
    			append_dev(ul, li0);
    			append_dev(ul, t1);
    			append_dev(ul, li1);
    			append_dev(ul, t3);
    			append_dev(ul, li2);
    		},
    		p: noop,
    		i: noop,
    		o: noop,
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(div);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_fragment$a.name,
    		type: "component",
    		source: "",
    		ctx
    	});

    	return block;
    }

    function instance$a($$self, $$props) {
    	let { $$slots: slots = {}, $$scope } = $$props;
    	validate_slots("Footer", slots, []);
    	const writable_props = [];

    	Object.keys($$props).forEach(key => {
    		if (!~writable_props.indexOf(key) && key.slice(0, 2) !== "$$") console.warn(`<Footer> was created with unknown prop '${key}'`);
    	});

    	return [];
    }

    class Footer extends SvelteComponentDev {
    	constructor(options) {
    		super(options);
    		init(this, options, instance$a, create_fragment$a, safe_not_equal, {});

    		dispatch_dev("SvelteRegisterComponent", {
    			component: this,
    			tagName: "Footer",
    			options,
    			id: create_fragment$a.name
    		});
    	}
    }

    /* src/frontend/Components/Form.svelte generated by Svelte v3.32.3 */
    const file$b = "src/frontend/Components/Form.svelte";

    function get_each_context$5(ctx, list, i) {
    	const child_ctx = ctx.slice();
    	child_ctx[6] = list[i];
    	return child_ctx;
    }

    // (32:0) {:else}
    function create_else_block_2(ctx) {
    	let div;
    	let p;
    	let strong;
    	let br;
    	let t1;
    	let div_intro;

    	const block = {
    		c: function create() {
    			div = element("div");
    			p = element("p");
    			strong = element("strong");
    			strong.textContent = "Vielen Dank für Ihre Anfrage!";
    			br = element("br");
    			t1 = text("Wir werden uns in Kürze bei Ihnen melden");
    			add_location(strong, file$b, 32, 70, 1085);
    			add_location(br, file$b, 32, 116, 1131);
    			add_location(p, file$b, 32, 67, 1082);
    			attr_dev(div, "class", "thank-you svelte-8dtc4q");
    			add_location(div, file$b, 32, 4, 1019);
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, div, anchor);
    			append_dev(div, p);
    			append_dev(p, strong);
    			append_dev(p, br);
    			append_dev(p, t1);
    		},
    		p: noop,
    		i: function intro(local) {
    			if (!div_intro) {
    				add_render_callback(() => {
    					div_intro = create_in_transition(div, fly, { x: 200, duration: 500, delay: 500 });
    					div_intro.start();
    				});
    			}
    		},
    		o: noop,
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(div);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_else_block_2.name,
    		type: "else",
    		source: "(32:0) {:else}",
    		ctx
    	});

    	return block;
    }

    // (15:0) {#if ! form.sent }
    function create_if_block$7(ctx) {
    	let form_1;
    	let div;
    	let t;
    	let current_block_type_index;
    	let if_block;
    	let form_1_name_value;
    	let form_1_class_value;
    	let form_1_outro;
    	let current;
    	let mounted;
    	let dispose;
    	let each_value = /*form*/ ctx[0].fieldsets;
    	validate_each_argument(each_value);
    	let each_blocks = [];

    	for (let i = 0; i < each_value.length; i += 1) {
    		each_blocks[i] = create_each_block$5(get_each_context$5(ctx, each_value, i));
    	}

    	const out = i => transition_out(each_blocks[i], 1, 1, () => {
    		each_blocks[i] = null;
    	});

    	let each_1_else = null;

    	if (!each_value.length) {
    		each_1_else = create_else_block_1(ctx);
    	}

    	const if_block_creators = [create_if_block_1$2, create_else_block];
    	const if_blocks = [];

    	function select_block_type_1(ctx, dirty) {
    		if (/*showNavbar*/ ctx[1]) return 0;
    		return 1;
    	}

    	current_block_type_index = select_block_type_1(ctx);
    	if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);

    	const block = {
    		c: function create() {
    			form_1 = element("form");
    			div = element("div");

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].c();
    			}

    			if (each_1_else) {
    				each_1_else.c();
    			}

    			t = space();
    			if_block.c();
    			attr_dev(div, "class", "fieldsets svelte-8dtc4q");
    			add_location(div, file$b, 16, 4, 557);
    			attr_dev(form_1, "name", form_1_name_value = /*form*/ ctx[0].name);
    			attr_dev(form_1, "class", form_1_class_value = "" + (null_to_empty(/*form*/ ctx[0].getClasses()) + " svelte-8dtc4q"));
    			add_location(form_1, file$b, 15, 0, 452);
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, form_1, anchor);
    			append_dev(form_1, div);

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].m(div, null);
    			}

    			if (each_1_else) {
    				each_1_else.m(div, null);
    			}

    			append_dev(form_1, t);
    			if_blocks[current_block_type_index].m(form_1, null);
    			current = true;

    			if (!mounted) {
    				dispose = listen_dev(form_1, "submit", prevent_default(/*submit_handler*/ ctx[5]), false, true, false);
    				mounted = true;
    			}
    		},
    		p: function update(ctx, dirty) {
    			if (dirty & /*form, update*/ 5) {
    				each_value = /*form*/ ctx[0].fieldsets;
    				validate_each_argument(each_value);
    				let i;

    				for (i = 0; i < each_value.length; i += 1) {
    					const child_ctx = get_each_context$5(ctx, each_value, i);

    					if (each_blocks[i]) {
    						each_blocks[i].p(child_ctx, dirty);
    						transition_in(each_blocks[i], 1);
    					} else {
    						each_blocks[i] = create_each_block$5(child_ctx);
    						each_blocks[i].c();
    						transition_in(each_blocks[i], 1);
    						each_blocks[i].m(div, null);
    					}
    				}

    				group_outros();

    				for (i = each_value.length; i < each_blocks.length; i += 1) {
    					out(i);
    				}

    				check_outros();

    				if (each_value.length) {
    					if (each_1_else) {
    						each_1_else.d(1);
    						each_1_else = null;
    					}
    				} else if (!each_1_else) {
    					each_1_else = create_else_block_1(ctx);
    					each_1_else.c();
    					each_1_else.m(div, null);
    				}
    			}

    			let previous_block_index = current_block_type_index;
    			current_block_type_index = select_block_type_1(ctx);

    			if (current_block_type_index === previous_block_index) {
    				if_blocks[current_block_type_index].p(ctx, dirty);
    			} else {
    				group_outros();

    				transition_out(if_blocks[previous_block_index], 1, 1, () => {
    					if_blocks[previous_block_index] = null;
    				});

    				check_outros();
    				if_block = if_blocks[current_block_type_index];

    				if (!if_block) {
    					if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);
    					if_block.c();
    				} else {
    					if_block.p(ctx, dirty);
    				}

    				transition_in(if_block, 1);
    				if_block.m(form_1, null);
    			}

    			if (!current || dirty & /*form*/ 1 && form_1_name_value !== (form_1_name_value = /*form*/ ctx[0].name)) {
    				attr_dev(form_1, "name", form_1_name_value);
    			}

    			if (!current || dirty & /*form*/ 1 && form_1_class_value !== (form_1_class_value = "" + (null_to_empty(/*form*/ ctx[0].getClasses()) + " svelte-8dtc4q"))) {
    				attr_dev(form_1, "class", form_1_class_value);
    			}
    		},
    		i: function intro(local) {
    			if (current) return;

    			for (let i = 0; i < each_value.length; i += 1) {
    				transition_in(each_blocks[i]);
    			}

    			transition_in(if_block);
    			if (form_1_outro) form_1_outro.end(1);
    			current = true;
    		},
    		o: function outro(local) {
    			each_blocks = each_blocks.filter(Boolean);

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				transition_out(each_blocks[i]);
    			}

    			transition_out(if_block);
    			form_1_outro = create_out_transition(form_1, fade, { duration: 500 });
    			current = false;
    		},
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(form_1);
    			destroy_each(each_blocks, detaching);
    			if (each_1_else) each_1_else.d();
    			if_blocks[current_block_type_index].d();
    			if (detaching && form_1_outro) form_1_outro.end();
    			mounted = false;
    			dispose();
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_if_block$7.name,
    		type: "if",
    		source: "(15:0) {#if ! form.sent }",
    		ctx
    	});

    	return block;
    }

    // (22:8) {:else}
    function create_else_block_1(ctx) {
    	let t;

    	const block = {
    		c: function create() {
    			t = text("JSON data failure.");
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, t, anchor);
    		},
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(t);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_else_block_1.name,
    		type: "else",
    		source: "(22:8) {:else}",
    		ctx
    	});

    	return block;
    }

    // (19:12) {#if fieldset.name === form.navigation.getCurrentFieldset().name }
    function create_if_block_2$2(ctx) {
    	let fieldset;
    	let current;

    	fieldset = new Fieldset$1({
    			props: { fieldset: /*fieldset*/ ctx[6] },
    			$$inline: true
    		});

    	fieldset.$on("update", /*update*/ ctx[2]);

    	const block = {
    		c: function create() {
    			create_component(fieldset.$$.fragment);
    		},
    		m: function mount(target, anchor) {
    			mount_component(fieldset, target, anchor);
    			current = true;
    		},
    		p: function update(ctx, dirty) {
    			const fieldset_changes = {};
    			if (dirty & /*form*/ 1) fieldset_changes.fieldset = /*fieldset*/ ctx[6];
    			fieldset.$set(fieldset_changes);
    		},
    		i: function intro(local) {
    			if (current) return;
    			transition_in(fieldset.$$.fragment, local);
    			current = true;
    		},
    		o: function outro(local) {
    			transition_out(fieldset.$$.fragment, local);
    			current = false;
    		},
    		d: function destroy(detaching) {
    			destroy_component(fieldset, detaching);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_if_block_2$2.name,
    		type: "if",
    		source: "(19:12) {#if fieldset.name === form.navigation.getCurrentFieldset().name }",
    		ctx
    	});

    	return block;
    }

    // (18:8) {#each form.fieldsets as fieldset}
    function create_each_block$5(ctx) {
    	let show_if = /*fieldset*/ ctx[6].name === /*form*/ ctx[0].navigation.getCurrentFieldset().name;
    	let if_block_anchor;
    	let current;
    	let if_block = show_if && create_if_block_2$2(ctx);

    	const block = {
    		c: function create() {
    			if (if_block) if_block.c();
    			if_block_anchor = empty();
    		},
    		m: function mount(target, anchor) {
    			if (if_block) if_block.m(target, anchor);
    			insert_dev(target, if_block_anchor, anchor);
    			current = true;
    		},
    		p: function update(ctx, dirty) {
    			if (dirty & /*form*/ 1) show_if = /*fieldset*/ ctx[6].name === /*form*/ ctx[0].navigation.getCurrentFieldset().name;

    			if (show_if) {
    				if (if_block) {
    					if_block.p(ctx, dirty);

    					if (dirty & /*form*/ 1) {
    						transition_in(if_block, 1);
    					}
    				} else {
    					if_block = create_if_block_2$2(ctx);
    					if_block.c();
    					transition_in(if_block, 1);
    					if_block.m(if_block_anchor.parentNode, if_block_anchor);
    				}
    			} else if (if_block) {
    				group_outros();

    				transition_out(if_block, 1, 1, () => {
    					if_block = null;
    				});

    				check_outros();
    			}
    		},
    		i: function intro(local) {
    			if (current) return;
    			transition_in(if_block);
    			current = true;
    		},
    		o: function outro(local) {
    			transition_out(if_block);
    			current = false;
    		},
    		d: function destroy(detaching) {
    			if (if_block) if_block.d(detaching);
    			if (detaching) detach_dev(if_block_anchor);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_each_block$5.name,
    		type: "each",
    		source: "(18:8) {#each form.fieldsets as fieldset}",
    		ctx
    	});

    	return block;
    }

    // (28:4) {:else}
    function create_else_block(ctx) {
    	let footer;
    	let current;
    	footer = new Footer({ $$inline: true });

    	const block = {
    		c: function create() {
    			create_component(footer.$$.fragment);
    		},
    		m: function mount(target, anchor) {
    			mount_component(footer, target, anchor);
    			current = true;
    		},
    		p: noop,
    		i: function intro(local) {
    			if (current) return;
    			transition_in(footer.$$.fragment, local);
    			current = true;
    		},
    		o: function outro(local) {
    			transition_out(footer.$$.fragment, local);
    			current = false;
    		},
    		d: function destroy(detaching) {
    			destroy_component(footer, detaching);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_else_block.name,
    		type: "else",
    		source: "(28:4) {:else}",
    		ctx
    	});

    	return block;
    }

    // (26:4) {#if showNavbar}
    function create_if_block_1$2(ctx) {
    	let navigation;
    	let current;

    	navigation = new Navigation$1({
    			props: { navigation: /*form*/ ctx[0].navigation },
    			$$inline: true
    		});

    	navigation.$on("navigate", /*update*/ ctx[2]);

    	const block = {
    		c: function create() {
    			create_component(navigation.$$.fragment);
    		},
    		m: function mount(target, anchor) {
    			mount_component(navigation, target, anchor);
    			current = true;
    		},
    		p: function update(ctx, dirty) {
    			const navigation_changes = {};
    			if (dirty & /*form*/ 1) navigation_changes.navigation = /*form*/ ctx[0].navigation;
    			navigation.$set(navigation_changes);
    		},
    		i: function intro(local) {
    			if (current) return;
    			transition_in(navigation.$$.fragment, local);
    			current = true;
    		},
    		o: function outro(local) {
    			transition_out(navigation.$$.fragment, local);
    			current = false;
    		},
    		d: function destroy(detaching) {
    			destroy_component(navigation, detaching);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_if_block_1$2.name,
    		type: "if",
    		source: "(26:4) {#if showNavbar}",
    		ctx
    	});

    	return block;
    }

    function create_fragment$b(ctx) {
    	let current_block_type_index;
    	let if_block;
    	let if_block_anchor;
    	let current;
    	const if_block_creators = [create_if_block$7, create_else_block_2];
    	const if_blocks = [];

    	function select_block_type(ctx, dirty) {
    		if (!/*form*/ ctx[0].sent) return 0;
    		return 1;
    	}

    	current_block_type_index = select_block_type(ctx);
    	if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);

    	const block = {
    		c: function create() {
    			if_block.c();
    			if_block_anchor = empty();
    		},
    		l: function claim(nodes) {
    			throw new Error("options.hydrate only works if the component was compiled with the `hydratable: true` option");
    		},
    		m: function mount(target, anchor) {
    			if_blocks[current_block_type_index].m(target, anchor);
    			insert_dev(target, if_block_anchor, anchor);
    			current = true;
    		},
    		p: function update(ctx, [dirty]) {
    			let previous_block_index = current_block_type_index;
    			current_block_type_index = select_block_type(ctx);

    			if (current_block_type_index === previous_block_index) {
    				if_blocks[current_block_type_index].p(ctx, dirty);
    			} else {
    				group_outros();

    				transition_out(if_blocks[previous_block_index], 1, 1, () => {
    					if_blocks[previous_block_index] = null;
    				});

    				check_outros();
    				if_block = if_blocks[current_block_type_index];

    				if (!if_block) {
    					if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);
    					if_block.c();
    				} else {
    					if_block.p(ctx, dirty);
    				}

    				transition_in(if_block, 1);
    				if_block.m(if_block_anchor.parentNode, if_block_anchor);
    			}
    		},
    		i: function intro(local) {
    			if (current) return;
    			transition_in(if_block);
    			current = true;
    		},
    		o: function outro(local) {
    			transition_out(if_block);
    			current = false;
    		},
    		d: function destroy(detaching) {
    			if_blocks[current_block_type_index].d(detaching);
    			if (detaching) detach_dev(if_block_anchor);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_fragment$b.name,
    		type: "component",
    		source: "",
    		ctx
    	});

    	return block;
    }

    function instance$b($$self, $$props, $$invalidate) {
    	let showNavbar;
    	let { $$slots: slots = {}, $$scope } = $$props;
    	validate_slots("Form", slots, []);
    	
    	let { formData } = $$props;
    	let { nonce } = $$props;
    	let form = new Form(formData, nonce);

    	let update = e => {
    		$$invalidate(0, form = e.detail);
    	};

    	const writable_props = ["formData", "nonce"];

    	Object.keys($$props).forEach(key => {
    		if (!~writable_props.indexOf(key) && key.slice(0, 2) !== "$$") console.warn(`<Form> was created with unknown prop '${key}'`);
    	});

    	function submit_handler(event) {
    		bubble($$self, event);
    	}

    	$$self.$$set = $$props => {
    		if ("formData" in $$props) $$invalidate(3, formData = $$props.formData);
    		if ("nonce" in $$props) $$invalidate(4, nonce = $$props.nonce);
    	};

    	$$self.$capture_state = () => ({
    		fly,
    		fade,
    		Form,
    		Fieldset: Fieldset$1,
    		Navigation: Navigation$1,
    		Footer,
    		formData,
    		nonce,
    		form,
    		update,
    		showNavbar
    	});

    	$$self.$inject_state = $$props => {
    		if ("formData" in $$props) $$invalidate(3, formData = $$props.formData);
    		if ("nonce" in $$props) $$invalidate(4, nonce = $$props.nonce);
    		if ("form" in $$props) $$invalidate(0, form = $$props.form);
    		if ("update" in $$props) $$invalidate(2, update = $$props.update);
    		if ("showNavbar" in $$props) $$invalidate(1, showNavbar = $$props.showNavbar);
    	};

    	if ($$props && "$$inject" in $$props) {
    		$$self.$inject_state($$props.$$inject);
    	}

    	$$self.$$.update = () => {
    		if ($$self.$$.dirty & /*form*/ 1) {
    			$$invalidate(1, showNavbar = form.navigation.getCurrentFieldset().name !== "start");
    		}
    	};

    	return [form, showNavbar, update, formData, nonce, submit_handler];
    }

    class Form_1 extends SvelteComponentDev {
    	constructor(options) {
    		super(options);
    		init(this, options, instance$b, create_fragment$b, safe_not_equal, { formData: 3, nonce: 4 });

    		dispatch_dev("SvelteRegisterComponent", {
    			component: this,
    			tagName: "Form_1",
    			options,
    			id: create_fragment$b.name
    		});

    		const { ctx } = this.$$;
    		const props = options.props || {};

    		if (/*formData*/ ctx[3] === undefined && !("formData" in props)) {
    			console.warn("<Form> was created without expected prop 'formData'");
    		}

    		if (/*nonce*/ ctx[4] === undefined && !("nonce" in props)) {
    			console.warn("<Form> was created without expected prop 'nonce'");
    		}
    	}

    	get formData() {
    		throw new Error("<Form>: Props cannot be read directly from the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}

    	set formData(value) {
    		throw new Error("<Form>: Props cannot be set directly on the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}

    	get nonce() {
    		throw new Error("<Form>: Props cannot be read directly from the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}

    	set nonce(value) {
    		throw new Error("<Form>: Props cannot be set directly on the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}
    }

    var name="immo-rating";var start="start";var classes=["immo-rating"];var fieldsets=[{label:"Wählen Sie die Immobilienart, welche Sie verkaufen wollen.",name:"start",percentage:10,fields:[{name:"immotype",type:"ImageChoice",choices:[{label:"Haus",value:"Haus",image:"/app/plugins/immo-rating/src/assets/images/house.png"},{label:"Wohnung",value:"Wohnung",image:"/app/plugins/immo-rating/src/assets/images/flat.png"},{label:"Grundstück",value:"Grundstück",image:"/app/plugins/immo-rating/src/assets/images/plot.png"},{label:"Gewerbe",value:"Gewerbe",image:"/app/plugins/immo-rating/src/assets/images/business.png"}],required:true,params:{setNextFieldset:true},validations:[{type:"inArray",values:["Haus","Wohnung","Grundstück","Gewerbe"],error:"Wert stimmt nicht mit den Vorgaben überein"}]}]},{label:"Welche Fläche hat das Grundstück der Immobilie?",name:"house/landarea",percentage:20,conditions:[{field:"immotype",value:"Haus",operator:"=="}],prevFieldset:"start",nextFieldset:"house/space",fieldsClasses:["house-left"],fields:[{name:"houseLandarea",type:"Range",label:"Grundstücksfläche",value:250,params:{min:1,max:500,step:1,unit:"m²"},validations:[{type:"min",value:1,error:"Die Grundstücksfläche ist zu klein."},{type:"max",value:500,error:"Die Grundstücksfläche ist zu groß."}]}]},{label:"Wie ist die gesamte Wohnfläche der Immobilie?",name:"house/space",percentage:30,nextFieldset:"house/floors",fieldsClasses:["house-left"],fields:[{name:"houseSpace",type:"Range",label:"Wohnfläche",value:250,params:{min:1,max:500,step:1,unit:"m²"},validations:[{type:"min",value:1,error:"Die Wohnfläche ist zu klein."},{type:"max",value:500,error:"Die Wohnfläche ist zu groß."}]}]},{label:"Wie viele Etagen hat das Haus?",name:"house/floors",percentage:40,nextFieldset:"house/rooms",fields:[{name:"houseFloors",type:"ImageChoice",choices:[{label:"Eine",value:"Eine",image:"/app/plugins/immo-rating/src/assets/images/one-floor.png"},{label:"Anderthalb",value:"Anderthalb",image:"/app/plugins/immo-rating/src/assets/images/one-and-a-half-floors.png"},{label:"Zwei",value:"Zwei",image:"/app/plugins/immo-rating/src/assets/images/two-floors.png"},{label:"Mehr als zwei",value:"Mehr als zwei",image:"/app/plugins/immo-rating/src/assets/images/more-than-two-floors.png"}],validations:[{type:"inArray",values:["Eine","Anderthalb","Zwei","Mehr als zwei"],error:"Bitte geben Sie an wie viele Etagen das Haus hat."}],params:{setNextFieldset:true}}]},{label:"Wie viele Zimmer hat das Haus?",name:"house/rooms",percentage:50,nextFieldset:"house/buildingyear",fieldsClasses:["house-left"],fields:[{name:"immoType",type:"Range",label:"Anzahl der Zimmer",value:4,params:{min:1,max:15,step:1},validations:[{type:"min",value:1,error:"Das Haus muss mindestens ein Zimmer haben."},{type:"max",value:15,error:"Das Haus darf maximal 15 Zimmer haben."}]}]},{label:"Wann wurde das Haus gebaut?",name:"house/buildingyear",percentage:60,nextFieldset:"house/parking",fieldsClasses:["house-left"],fields:[{name:"houseBuildingyear",type:"Range",label:"Baujahr",value:2000,params:{min:1800,max:2021,step:1},validations:[{type:"min",value:1800,error:"Das Baujahr darf nicht älter als 1800 sein."},{type:"max",value:2021,error:"Das Haus muss schon fertig gebaut sein."}]}]},{label:"Welche Parkmöglichkeiten sind am Haus vorhanden?",name:"house/parking",percentage:70,nextFieldset:"house/selldate",fields:[{name:"houseParking",type:"ImageChoice",choices:[{label:"Garage",value:"Garage",image:"/app/plugins/immo-rating/src/assets/images/garage.png"},{label:"Carport",value:"Carport",image:"/app/plugins/immo-rating/src/assets/images/carport.png"},{label:"Parkplatz",value:"Parkplatz",image:"/app/plugins/immo-rating/src/assets/images/parking-spot.png"},{label:"Nicht vorhanden",value:"Nicht vorhanden",image:"/app/plugins/immo-rating/src/assets/images/no-parking.png"}],validations:[{type:"inArray",values:["Garage","Carport","Parkplatz","Nicht vorhanden"],error:"Bitte geben Sie an welche Parkmöglichkeiten das Haus hat."}],params:{setNextFieldset:true}}]},{label:"Wann möchten Sie Ihre Immobilie Verkaufen?",name:"house/selldate",percentage:80,nextFieldset:"house/address",fieldsClasses:["house-left"],fields:[{name:"houseContact",type:"RadioChoice",choices:[{label:"in 1-3 Monaten",value:"in 1-3 Monaten"},{label:"in 4-6 Monaten",value:"in 4-6 Monaten"},{label:"in mehr als 6 Monaten",value:"in mehr als 6 Monaten"},{label:"Im Moment noch nicht",value:"Im Moment noch nicht"}],validations:[{type:"inArray",values:["in 1-3 Monaten","in 4-6 Monaten","in mehr als 6 Monaten","Im Moment noch nicht"],error:"Bitte treffen Sie eine Auswahl."}],params:{setNextFieldset:true}}]},{label:"Wie ist die Adresse der Immobilie?",name:"house/address",percentage:90,nextFieldset:"house/contact",fieldsClasses:["house-left"],fields:[{name:"street",label:"Straße und Hausnummer",type:"Text",required:true,validations:[{type:"string",error:"Der Angegebene Wert muss eine Zeichenkette sein"},{type:"minLength",value:4,error:"Mindestens 4 Zeichen"},{type:"maxLength",value:100,error:"Maximal 100 Zeichen"}]},{name:"postcode",label:"Postleitzahl",type:"Text",required:true,validations:[{type:"string",error:"Der Angegebene Wert muss eine Zeichenkette sein"},{type:"minLength",value:5,error:"Mindestens 5 Zeichen"},{type:"maxLength",value:5,error:"Maximal 5 Zeichen"}]},{name:"city",label:"Ort",type:"Text",required:true,validations:[{type:"string",error:"Der Angegebene Wert muss eine Zeichenkette sein"},{type:"minLength",value:4,error:"Mindestens 3 Zeichen"},{type:"maxLength",value:100,error:"Maximal 100 Zeichen"}]}]},{label:"Wie lauten Ihre Kontaktdaten?",name:"house/contact",percentage:100,submission:{fieldsets:["start","house/landarea","house/livingspace","house/floors","house/rooms","house/buildingyear","house/parking","house/selldate","house/address","house/contact"],url:"/api/awsm/v1/submission",method:"POST"},fieldsClasses:["house-left"],fields:[{name:"name",label:"Name",type:"Text",placeholder:"Ihr Name",required:true,validations:[{type:"string",error:"Der Angegebene Wert muss eine Zeichenkette sein"},{type:"minLength",value:3,error:"Mindestens 3 Zeichen"},{type:"maxLength",value:100,error:"Maximal 100 Zeichen"}]},{name:"email",label:"Email",type:"Text",placeholder:"Ihr Email",required:true,validations:[{type:"email",error:"Bitte geben Sie eine gültige Email-Adresse ein"}]},{name:"phone",label:"Telefon",type:"Text",placeholder:"+49 030 123456789",required:true,validations:[{type:"string",error:"Der Angegebene Wert muss eine Zeichenkette sein"},{type:"minLength",value:8,error:"Mindestens 3 Zeichen"},{type:"maxLength",value:100,error:"Maximal 100 Zeichen"}]}]},{label:"Wie viel m² hat die Wohnung?",name:"flat/livingspace",percentage:12.5,prevFieldset:"start",nextFieldset:"flat/buildingyear",conditions:[{field:"immotype",value:"Wohnung",operator:"=="}],fieldsClasses:["flat-left"],fields:[{name:"immoType",type:"Range",label:"Wohnfläche",value:100,params:{min:1,max:250,step:1,unit:"m²"},validations:[{type:"min",value:1,error:"Die Grundstücksfläche ist zu klein."},{type:"max",value:500,error:"Die Grundstücksfläche ist zu groß."}]}]},{label:"Welches Baujahr hat das Wohnhaus?",name:"flat/buildingyear",percentage:25,nextFieldset:"flat/parking",fieldsClasses:["flat-left"],fields:[{name:"immoType",type:"Range",label:"Baujahr",value:2000,params:{min:1800,max:2021,step:1},validations:[{type:"min",value:1800,error:"Das Baujahr darf nicht älter als 1800 sein."},{type:"max",value:2021,error:"Das Haus muss schon fertig gebaut sein."}]}]},{label:"Welche Parkmöglichkeiten hat die Wohnung?",name:"flat/parking",percentage:37.5,nextFieldset:"flat/kitchen",fields:[{name:"flatParking",type:"ImageChoice",choices:[{label:"Garage",value:"Garage",image:"/app/plugins/immo-rating/src/assets/images/garage.png"},{label:"Carport",value:"Carport",image:"/app/plugins/immo-rating/src/assets/images/carport.png"},{label:"Parkplatz",value:"Parkplatz",image:"/app/plugins/immo-rating/src/assets/images/parking-spot.png"},{label:"Nicht vorhanden",value:"Nicht vorhanden",image:"/app/plugins/immo-rating/src/assets/images/no-parking.png"}],validations:[{type:"inArray",values:["Garage","Carport","Parkplatz","Nicht vorhanden"],error:"Bitte geben Sie eine Parkmöglichkeit an."}],params:{setNextFieldset:true}}]},{label:"Ist eine Einbauküche vorhanden?",name:"flat/kitchen",percentage:50,nextFieldset:"flat/elevator",fields:[{name:"flatKitchen",type:"ImageChoice",choices:[{label:"Neuwertig",value:"Neuwertig",image:"/app/plugins/immo-rating/src/assets/images/kitchen-like-new.png"},{label:"Gebraucht",value:"Gebraucht",image:"/app/plugins/immo-rating/src/assets/images/kitchen-used.png"},{label:"Nicht vorhanden",value:"Nicht vorhanden",image:"/app/plugins/immo-rating/src/assets/images/no-kitchen.png"}],validations:[{type:"inArray",values:["Neuwertig","Gebraucht","Nicht vorhanden"],error:"Bitte wählen Sie eine Option."}],params:{setNextFieldset:true}}]},{label:"Verfügt das Gebäude über einen Personenaufzug?",name:"flat/elevator",percentage:62.5,nextFieldset:"flat/selldate",fieldsClasses:["flat-left"],fields:[{name:"flatElevator",type:"RadioChoice",choices:[{label:"Ja, es existiert ein Personenaufzug",value:"Ja"},{label:"Nein, es existiert kein Personenaufzug",value:"Nein"}],validations:[{type:"inArray",values:["Ja","Nein"],error:"Bitte wählen Sie eine Option."}],params:{setNextFieldset:true}}]},{label:"Wann möchten Sie Ihre Immobilie Verkaufen?",name:"flat/selldate",percentage:75,nextFieldset:"flat/address",fieldsClasses:["flat-left"],fields:[{name:"houseFloors",type:"RadioChoice",choices:[{label:"in 1-3 Monaten",value:"in 1-3 Monaten"},{label:"in 4-6 Monaten",value:"in 4-6 Monaten"},{label:"in mehr als 6 Monaten",value:"in mehr als 6 Monaten"},{label:"Im Moment noch nicht",value:"Im Moment noch nicht"}],validations:[{type:"inArray",values:["in 1-3 Monaten","in 4-6 Monaten","in mehr als 6 Monaten","Im Moment noch nicht"],error:"Bitte wählen Sie eine Option."}],params:{setNextFieldset:true}}]},{label:"Wie ist die Adresse der Immobilie?",name:"flat/address",percentage:87.5,nextFieldset:"flat/contact",fieldsClasses:["house-left"],fields:[{name:"street",label:"Straße und Hausnummer",type:"Text",required:true,validations:[{type:"string",error:"Der Angegebene Wert muss eine Zeichenkette sein"},{type:"minLength",value:4,error:"Mindestens 4 Zeichen"},{type:"maxLength",value:100,error:"Maximal 100 Zeichen"}]},{name:"postcode",label:"Postleitzahl",type:"Text",required:true,validations:[{type:"string",error:"Der Angegebene Wert muss eine Zeichenkette sein"},{type:"minLength",value:5,error:"Mindestens 5 Zeichen"},{type:"maxLength",value:5,error:"Maximal 5 Zeichen"}]},{name:"city",label:"Ort",type:"Text",required:true,validations:[{type:"string",error:"Der Angegebene Wert muss eine Zeichenkette sein"},{type:"minLength",value:4,error:"Mindestens 3 Zeichen"},{type:"maxLength",value:100,error:"Maximal 100 Zeichen"}]}]},{label:"Wie lauten Ihre Kontaktdaten?",name:"flat/contact",percentage:100,submission:{fieldsets:["start","flat/livingspace","flat/buildingyear","flat/parking","flat/kitchen","flat/elevator","flat/selldate","flat/address","flat/contact"],url:"/api/awsm/v1/submission",method:"POST"},fieldsClasses:["flat-left"],fields:[{name:"name",label:"Name",type:"Text",placeholder:"Ihr Name",required:true,validations:[{type:"string",error:"Der Angegebene Wert muss eine Zeichenkette sein"},{type:"minLength",value:3,error:"Mindestens 3 Zeichen"},{type:"maxLength",value:100,error:"Maximal 100 Zeichen"}]},{name:"email",label:"Email",type:"Text",placeholder:"Ihr Email",required:true,validations:[{type:"email",error:"Bitte geben Sie eine gültige Email-Adresse ein"}]},{name:"phone",label:"Telefon",type:"Text",placeholder:"+49 030 123456789",required:true,validations:[{type:"string",error:"Der Angegebene Wert muss eine Zeichenkette sein"},{type:"minLength",value:8,error:"Mindestens 3 Zeichen"},{type:"maxLength",value:100,error:"Maximal 100 Zeichen"}]}]},{label:"Welche Fläche hat das Grundstück der Immobilie?",name:"plot/landarea",percentage:16.6,conditions:[{field:"immotype",value:"Grundstück",operator:"=="}],prevFieldset:"start",nextFieldset:"plot/developed",fieldsClasses:["plot-left"],fields:[{name:"plotLandarea",type:"Range",label:"Grundstücksfläche",value:250,params:{min:1,max:10000,step:1,unit:"m²"},validations:[{type:"min",value:1,error:"Die Grundstücksfläche ist zu klein."},{type:"max",value:10000,error:"Die Grundstücksfläche ist zu groß."}]}]},{label:"Ist das Grundstück erschlossen?",name:"plot/developed",percentage:32.2,nextFieldset:"plot/development-possibilities",fieldsClasses:["plot-left"],fields:[{name:"plotDeveloped",type:"RadioChoice",choices:[{label:"Erschlossen",value:"Erschlossen"},{label:"Teilerschlossen",value:"Teilerschlossen"},{label:"Vollerschlossen",value:"Vollerschlossen"}],validations:[{type:"inArray",values:["Erschlossen","Teilerschlossen","Vollerschlossen"],error:"Bitte wählen Sie eine Option."}],params:{setNextFieldset:true}}]},{label:"Welche Bebauungsmöglichkeiten hat das Grundstück?",name:"plot/development-possibilities",percentage:48.8,nextFieldset:"plot/selldate",fieldsClasses:["plot-left"],fields:[{name:"plotDevelopmentPossibilities",type:"RadioChoice",choices:[{label:"eingeschränkt bebaubar",value:"eingeschränkt bebaubar"},{label:"nicht bebaubar",value:"nicht bebaubar"},{label:"weiß nicht",value:"weiß nicht"}],validations:[{type:"inArray",values:["eingeschränkt bebaubar","nicht bebaubar","weiß nicht"],error:"Bitte wählen Sie eine Option."}],params:{setNextFieldset:true}}]},{label:"Wann möchten Sie Ihr Grundstück Verkaufen?",name:"plot/selldate",percentage:66.4,nextFieldset:"plot/address",fieldsClasses:["plot-left"],fields:[{name:"plotSelldate",type:"RadioChoice",choices:[{label:"in 1-3 Monaten",value:"in 1-3 Monaten"},{label:"in 4-6 Monaten",value:"in 4-6 Monaten"},{label:"in mehr als 6 Monaten",value:"in mehr als 6 Monaten"},{label:"Im Moment noch nicht",value:"Im Moment noch nicht"}],validations:[{type:"inArray",values:["in 1-3 Monaten","in 4-6 Monaten","in mehr als 6 Monaten","Im Moment noch nicht"],error:"Bitte wählen Sie eine Option."}],params:{setNextFieldset:true}}]},{label:"Wie ist die Adresse der Immobilie?",name:"plot/address",percentage:83,nextFieldset:"plot/contact",fieldsClasses:["house-left"],fields:[{name:"street",label:"Straße und Hausnummer",type:"Text",required:true,validations:[{type:"string",error:"Der Angegebene Wert muss eine Zeichenkette sein"},{type:"minLength",value:4,error:"Mindestens 4 Zeichen"},{type:"maxLength",value:100,error:"Maximal 100 Zeichen"}]},{name:"postcode",label:"Postleitzahl",type:"Text",required:true,validations:[{type:"string",error:"Der Angegebene Wert muss eine Zeichenkette sein"},{type:"minLength",value:5,error:"Mindestens 5 Zeichen"},{type:"maxLength",value:5,error:"Maximal 5 Zeichen"}]},{name:"city",label:"Ort",type:"Text",required:true,validations:[{type:"string",error:"Der Angegebene Wert muss eine Zeichenkette sein"},{type:"minLength",value:4,error:"Mindestens 3 Zeichen"},{type:"maxLength",value:100,error:"Maximal 100 Zeichen"}]}]},{label:"Wie lauten Ihre Kontaktdaten?",name:"plot/contact",percentage:100,submission:{fieldsets:["start","plot/landarea","plot/developed","plot/development-possibilities","plot/selldate","plot/address","plot/contact"],url:"/api/awsm/v1/submission",method:"POST"},fieldsClasses:["plot-left"],fields:[{name:"name",label:"Name",type:"Text",placeholder:"Ihr Name",required:true,validations:[{type:"string",error:"Der Angegebene Wert muss eine Zeichenkette sein"},{type:"minLength",value:3,error:"Mindestens 3 Zeichen"},{type:"maxLength",value:100,error:"Maximal 100 Zeichen"}]},{name:"email",label:"Email",type:"Text",placeholder:"Ihr Email",required:true,validations:[{type:"email",error:"Bitte geben Sie eine gültige Email-Adresse ein"}]},{name:"phone",label:"Telefon",type:"Text",placeholder:"+49 030 123456789",required:true,validations:[{type:"string",error:"Der Angegebene Wert muss eine Zeichenkette sein"},{type:"minLength",value:8,error:"Mindestens 3 Zeichen"},{type:"maxLength",value:100,error:"Maximal 100 Zeichen"}]}]},{label:"Um welche Gebäudeart handelt es sich",name:"business/type",percentage:16.6,prevFieldset:"start",nextFieldset:"business/landarea",conditions:[{field:"immotype",value:"Gewerbe",operator:"=="}],fields:[{name:"businessType",type:"ImageChoice",choices:[{label:"Büro-/Lagergebäude",value:"Büro-/Lagergebäude",image:"/app/plugins/immo-rating/src/assets/images/office-warehouse.png"},{label:"Wohn-/Geschäftsgebäude",value:"Wohn-/Geschäftsgebäude",image:"/app/plugins/immo-rating/src/assets/images/living-business.png"},{label:"Industrie-/Gewerbegebäude",value:"Industrie-/Gewerbegebäude",image:"/app/plugins/immo-rating/src/assets/images/industry-business.png"}],validations:[{type:"inArray",values:["Büro-/Lagergebäude","Wohn-/Geschäftsgebäude","Industrie-/Gewerbegebäude"],error:"Bitte geben Sie an um welche Gebäudeart es sich handelt."}],params:{setNextFieldset:true}}]},{label:"Welche Fläche hat das Gebäude?",name:"business/landarea",percentage:32.2,nextFieldset:"business/buildingyear",fieldsClasses:["business-left"],fields:[{name:"house/landarea",type:"Range",label:"Grundstücksfläche",value:250,params:{min:1,max:20000,step:1,unit:"m²"},validations:[{type:"min",value:1,error:"Die Gebäudefläche ist zu klein."},{type:"max",value:20000,error:"Die Gebäudefläche ist zu groß."}]}]},{label:"Welches Baujahr hat das Gebäude?",name:"business/buildingyear",percentage:49.8,nextFieldset:"business/selldate",fieldsClasses:["business-left"],fields:[{name:"immoType",type:"Range",label:"Baujahr",value:2000,params:{min:1800,max:2021,step:1},validations:[{type:"min",value:1800,error:"Das Baujahr darf nicht älter als 1800 sein."},{type:"max",value:2021,error:"Das Haus muss schon fertig gebaut sein."}]}]},{label:"Wann möchten Sie Ihre Immobilie Verkaufen?",name:"business/selldate",percentage:66.4,nextFieldset:"business/address",fieldsClasses:["business-left"],fields:[{name:"businessSelldate",type:"RadioChoice",choices:[{label:"in 1-3 Monaten",value:"in 1-3 Monaten"},{label:"in 4-6 Monaten",value:"in 4-6 Monaten"},{label:"in mehr als 6 Monaten",value:"in mehr als 6 Monaten"},{label:"Im Moment noch nicht",value:"Im Moment noch nicht"}],validations:[{type:"inArray",values:["in 1-3 Monaten","in 4-6 Monaten","in mehr als 6 Monaten","Im Moment noch nicht"],error:"Bitte treffen Sie eine Auswahl."}],params:{setNextFieldset:true}}]},{label:"Wie ist die Adresse der Immobilie?",name:"business/address",percentage:83,nextFieldset:"business/contact",fieldsClasses:["house-left"],fields:[{name:"street",label:"Straße und Hausnummer",type:"Text",required:true,validations:[{type:"string",error:"Der Angegebene Wert muss eine Zeichenkette sein"},{type:"minLength",value:4,error:"Mindestens 4 Zeichen"},{type:"maxLength",value:100,error:"Maximal 100 Zeichen"}]},{name:"postcode",label:"Postleitzahl",type:"Text",required:true,validations:[{type:"string",error:"Der Angegebene Wert muss eine Zeichenkette sein"},{type:"minLength",value:5,error:"Mindestens 5 Zeichen"},{type:"maxLength",value:5,error:"Maximal 5 Zeichen"}]},{name:"city",label:"Ort",type:"Text",required:true,validations:[{type:"string",error:"Der Angegebene Wert muss eine Zeichenkette sein"},{type:"minLength",value:4,error:"Mindestens 3 Zeichen"},{type:"maxLength",value:100,error:"Maximal 100 Zeichen"}]}]},{label:"Wie lauten Ihre Kontaktdaten?",name:"business/contact",percentage:100,submission:{fieldsets:["start","business/type","business/landarea","business/buildingyear","business/selldate","business/address","business/contact"],url:"/api/awsm/v1/submission",method:"POST"},fieldsClasses:["business-left"],fields:[{name:"name",label:"Name",type:"Text",placeholder:"Ihr Name",required:true,validations:[{type:"string",error:"Der Angegebene Wert muss eine Zeichenkette sein"},{type:"minLength",value:3,error:"Mindestens 3 Zeichen"},{type:"maxLength",value:100,error:"Maximal 100 Zeichen"}]},{name:"email",label:"Email",type:"Text",placeholder:"Ihr Email",required:true,validations:[{type:"email",error:"Bitte geben Sie eine gültige Email-Adresse ein"}]},{name:"phone",label:"Telefon",type:"Text",placeholder:"+49 030 123456789",required:true,validations:[{type:"string",error:"Der Angegebene Wert muss eine Zeichenkette sein"},{type:"minLength",value:8,error:"Mindestens 3 Zeichen"},{type:"maxLength",value:100,error:"Maximal 100 Zeichen"}]}]}];var FormData$1 = {name:name,start:start,classes:classes,fieldsets:fieldsets};

    /* src/frontend/App.svelte generated by Svelte v3.32.3 */

    function create_fragment$c(ctx) {
    	let form;
    	let current;

    	form = new Form_1({
    			props: {
    				formData: FormData$1,
    				nonce: /*nonce*/ ctx[0]
    			},
    			$$inline: true
    		});

    	const block = {
    		c: function create() {
    			create_component(form.$$.fragment);
    		},
    		l: function claim(nodes) {
    			throw new Error("options.hydrate only works if the component was compiled with the `hydratable: true` option");
    		},
    		m: function mount(target, anchor) {
    			mount_component(form, target, anchor);
    			current = true;
    		},
    		p: function update(ctx, [dirty]) {
    			const form_changes = {};
    			if (dirty & /*nonce*/ 1) form_changes.nonce = /*nonce*/ ctx[0];
    			form.$set(form_changes);
    		},
    		i: function intro(local) {
    			if (current) return;
    			transition_in(form.$$.fragment, local);
    			current = true;
    		},
    		o: function outro(local) {
    			transition_out(form.$$.fragment, local);
    			current = false;
    		},
    		d: function destroy(detaching) {
    			destroy_component(form, detaching);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_fragment$c.name,
    		type: "component",
    		source: "",
    		ctx
    	});

    	return block;
    }

    function instance$c($$self, $$props, $$invalidate) {
    	let { $$slots: slots = {}, $$scope } = $$props;
    	validate_slots("App", slots, []);
    	let { nonce } = $$props;
    	const writable_props = ["nonce"];

    	Object.keys($$props).forEach(key => {
    		if (!~writable_props.indexOf(key) && key.slice(0, 2) !== "$$") console.warn(`<App> was created with unknown prop '${key}'`);
    	});

    	$$self.$$set = $$props => {
    		if ("nonce" in $$props) $$invalidate(0, nonce = $$props.nonce);
    	};

    	$$self.$capture_state = () => ({ Form: Form_1, FormData: FormData$1, nonce });

    	$$self.$inject_state = $$props => {
    		if ("nonce" in $$props) $$invalidate(0, nonce = $$props.nonce);
    	};

    	if ($$props && "$$inject" in $$props) {
    		$$self.$inject_state($$props.$$inject);
    	}

    	return [nonce];
    }

    class App extends SvelteComponentDev {
    	constructor(options) {
    		super(options);
    		init(this, options, instance$c, create_fragment$c, safe_not_equal, { nonce: 0 });

    		dispatch_dev("SvelteRegisterComponent", {
    			component: this,
    			tagName: "App",
    			options,
    			id: create_fragment$c.name
    		});

    		const { ctx } = this.$$;
    		const props = options.props || {};

    		if (/*nonce*/ ctx[0] === undefined && !("nonce" in props)) {
    			console.warn("<App> was created without expected prop 'nonce'");
    		}
    	}

    	get nonce() {
    		throw new Error("<App>: Props cannot be read directly from the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}

    	set nonce(value) {
    		throw new Error("<App>: Props cannot be set directly on the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}
    }

    let element$1 = document.querySelector('#immorating');
    console.log("element");
    console.log(element$1);
    if (element$1 !== null) {
        let nonce = element$1.dataset.nonce;
        new App({
            target: element$1,
            props: {
                nonce
            }
        });
    }
    var app$1 = app;

    return app$1;

}());
//# sourceMappingURL=bundle.js.map
