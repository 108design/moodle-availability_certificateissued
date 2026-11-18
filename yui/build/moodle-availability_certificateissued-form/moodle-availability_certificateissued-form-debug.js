/*!
 * This file is part of Moodle - https://moodle.org/
 *
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle. If not, see <https://www.gnu.org/licenses/>.
 *
 * @package    availability_certificateissued
 * @copyright  2026 Andreas Giesen <andreas@108design.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
YUI.add("moodle-availability_certificateissued-form", function(Y) {
/**
 * Certificate issue condition editor.
 * @module moodle-availability_certificateissued-form
 */
M.availability_certificateissued = M.availability_certificateissued || {};
M.availability_certificateissued.form = Y.Object(M.core_availability.plugin);

M.availability_certificateissued.form.initInner = function(templates, activities, site, courseid, help) {
    this.templates = templates;
    this.activities = activities;
    this.site = site;
    this.courseid = courseid;
    this.help = help;
};

M.availability_certificateissued.form.getNode = function(json) {
    var self = this;
    var component = 'availability_certificateissued';
    var str = function(key) { return M.util.get_string(key, component); };
    var node = Y.Node.create('<div class="availability-certificateissued-editor"></div>');
    var serial = Y.guid('certissued-');
    var addSelect = function(name, title, options, help, detail) {
        var row = Y.Node.create('<div class="availability-certificateissued-row"></div>');
        if (detail) row.addClass('availability-certificateissued-has-detail');
        var label = Y.Node.create('<label></label>');
        label.set('for', serial + name);
        label.set('text', title);
        var labelcell = Y.Node.create('<span class="availability-certificateissued-label"></span>');
        labelcell.append(label);
        if (help) labelcell.append(help);
        row.append(labelcell);
        var select = Y.Node.create('<select class="form-select custom-select"></select>');
        select.setAttrs({name: name, id: serial + name});
        options.forEach(function(item) {
            var option = Y.Node.create('<option></option>');
            option.setAttrs({value: String(item.id), text: item.name});
            select.append(option);
        });
        row.append(select);
        if (detail) row.append(detail);
        node.append(row);
        return select;
    };
    var picker;
    if (self.activities.length) {
        picker = addSelect('activitypicker', str('activitypicker'),
            [{id: 0, name: M.util.get_string('choosedots', 'moodle')}].concat(self.activities), self.help.activity);
    }
    var templateOptions = [{id: 0, name: M.util.get_string('choosedots', 'moodle')}].concat(self.templates);
    var template = addSelect('templateid', str('template'), templateOptions, null, self.help.details);
    if (json.templateid && !self.templates.some(function(item) { return item.id === json.templateid; })) {
        template.append(Y.Node.create('<option></option>').setAttrs({
            value: String(json.templateid), text: str('invalidselection')
        }));
        node.append(Y.Node.create('<p class="text-danger"></p>').set('text', str('invalidselection')));
    }
    var enums = function(prefix, values) {
        return values.map(function(value) { return {id: value, name: str(prefix + '_' + value)}; });
    };
    var scope = addSelect('scope', str('scope'), enums('scope', ['course', 'any']), self.help.scope);
    if (picker) {
        picker.on('change', function() {
            var activity = self.activities.find(function(item) { return String(item.id) === picker.get('value'); });
            if (activity) {
                template.set('value', String(activity.templateid));
                scope.set('value', 'course');
                node.setData('site', self.site);
                picker.set('value', '0');
                M.core_availability.form.update();
            }
        });
    }
    addSelect('received', str('requirement'), [
        {id: '0', name: str('notreceived')}, {id: '1', name: str('received')}
    ]);
    addSelect('validity', str('validity'), enums('validity', ['all', 'valid', 'expired']), self.help.status, self.help.states);
    addSelect('archive', str('archive'), enums('archive', ['all', 'current', 'archived']));
    var hint = Y.Node.create('<p class="small mb-1"></p>').set('text', str('activityhint') + ' ');
    node.append(hint);
    var defaults = {templateid: 0, scope: 'course', received: '0', validity: 'all', archive: 'all'};
    Object.keys(defaults).forEach(function(name) {
        var value = defaults[name];
        if (json[name] !== undefined && json.creating === undefined) {
            value = name === 'received' ? (json[name] ? '1' : '0') : json[name];
        }
        node.one('select[name=' + name + ']').set('value', String(value));
    });
    // A restored foreign reference must be deliberately reselected before it can be saved.
    node.setData('site', json.creating === undefined && json.site ? json.site : self.site);
    template.on('change', function() { node.setData('site', self.site); });
    node.delegate('change', function() { M.core_availability.form.update(); }, 'select');
    return node;
};

M.availability_certificateissued.form.fillValue = function(value, node) {
    value.templateid = parseInt(node.one('select[name=templateid]').get('value'), 10) || 0;
    value.received = node.one('select[name=received]').get('value') === '1';
    value.scope = node.one('select[name=scope]').get('value');
    value.validity = node.one('select[name=validity]').get('value');
    value.archive = node.one('select[name=archive]').get('value');
    value.site = node.getData('site');
    value.boundcourse = this.courseid;
    var template = this.templates.find(function(item) { return item.id === value.templateid; });
    value.binding = template ? template.bindings[value.scope] : '';
};

M.availability_certificateissued.form.fillErrors = function(errors, node) {
    var id = parseInt(node.one('select[name=templateid]').get('value'), 10);
    if (!this.templates.some(function(item) { return item.id === id; }) || node.getData('site') !== this.site) {
        errors.push('availability_certificateissued:missing');
    }
};

}, "0.1.0", {requires:["moodle-core_availability-form"]});
