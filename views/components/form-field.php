<?php
/**
 * components/form-field.php  -  label + input + help + error, one consistent look
 *
 *   <?= component('form-field', ['name' => 'email', 'label' => 'University email', 'type' => 'email',
 *         'icon' => 'envelope', 'value' => $old['email'] ?? '', 'error' => $errors['email'] ?? '', 'required' => true]) ?>
 *   <?= component('form-field', ['name' => 'password', 'label' => 'Password', 'type' => 'password', 'icon' => 'lock', 'toggle' => true]) ?>
 *   <?= component('form-field', ['name' => 'subject_id', 'label' => 'Subject', 'type' => 'select',
 *         'options' => [1 => 'Software Engineering', 2 => 'Databases'], 'value' => 2, 'placeholder' => 'Choose a subject']) ?>
 *   <?= component('form-field', ['name' => 'file', 'label' => 'File', 'type' => 'file', 'attrs' => ['accept' => '.pdf,.docx,.pptx']]) ?>
 *
 * type         text | email | password | number | date | time | datetime-local | textarea | select | file | checkbox
 * name, label  required
 * value        current value (for select: the selected key; for checkbox: true = checked)
 * options      select only: [value => label]
 * placeholder  input placeholder / first empty option in a select
 * help         small hint under the field     error  message under the field (turns it red)
 * icon         Bootstrap Icons name shown inside the field (text-like inputs only)
 * toggle       password only: eye button (needs the data-toggle-password handler in app.js)
 * rows         textarea height (default 4)    id  defaults to field-<name>
 * attrs        extra attributes on the input, e.g. ['maxlength' => 120, 'autocomplete' => 'email']
 */
$name        = $name ?? '';
$label       = $label ?? '';
$type        = $type ?? 'text';
$value       = $value ?? '';
$options     = $options ?? [];
$placeholder = $placeholder ?? '';
$help        = $help ?? '';
$error       = $error ?? '';
$icon        = $icon ?? '';
$toggle      = !empty($toggle) && $type === 'password';
$rows        = $rows ?? 4;
$required    = !empty($required);
$attrs       = $attrs ?? [];
$id          = $id ?? 'field-' . $name;

$hasError  = $error !== '';
$describe  = $hasError ? $id . '-error' : ($help !== '' ? $id . '-help' : null);
$withIcon  = $icon !== '' && !in_array($type, ['textarea', 'select', 'file', 'checkbox'], true);
$common    = array_merge([
    'id'               => $id,
    'name'             => $name,
    'required'         => $required,
    'aria-invalid'     => $hasError ? 'true' : null,
    'aria-describedby' => $describe,
], $attrs);
$wrapClass = 'sc-field' . ($withIcon ? ' sc-field--icon' : '') . ($toggle ? ' sc-field--toggle' : '');
?>
<div class="<?= e($wrapClass) ?>">
<?php if ($type === 'checkbox'): ?>
    <div class="form-check">
        <input class="form-check-input<?= $hasError ? ' is-invalid' : '' ?>" type="checkbox" value="1"<?= $value ? ' checked' : '' ?><?= html_attrs($common) ?>>
        <label class="form-check-label" for="<?= e($id) ?>"><?= e($label) ?></label>
    </div>
<?php else: ?>
    <label class="form-label" for="<?= e($id) ?>"><?= e($label) ?><?php if ($required): ?> <span class="sc-field__req" aria-hidden="true">*</span><?php endif; ?></label>
    <div class="sc-field__control">
        <?php if ($withIcon): ?><i class="bi bi-<?= e($icon) ?> sc-field__icon" aria-hidden="true"></i><?php endif; ?>

        <?php if ($type === 'textarea'): ?>
            <textarea class="form-control<?= $hasError ? ' is-invalid' : '' ?>" rows="<?= (int) $rows ?>" placeholder="<?= e($placeholder) ?>"<?= html_attrs($common) ?>><?= e((string) $value) ?></textarea>

        <?php elseif ($type === 'select'): ?>
            <select class="form-select<?= $hasError ? ' is-invalid' : '' ?>"<?= html_attrs($common) ?>>
                <?php if ($placeholder !== ''): ?><option value=""><?= e($placeholder) ?></option><?php endif; ?>
                <?php foreach ($options as $optValue => $optLabel): ?>
                    <option value="<?= e((string) $optValue) ?>"<?= (string) $optValue === (string) $value ? ' selected' : '' ?>><?= e((string) $optLabel) ?></option>
                <?php endforeach; ?>
            </select>

        <?php elseif ($type === 'file'): ?>
            <input class="form-control<?= $hasError ? ' is-invalid' : '' ?>" type="file"<?= html_attrs($common) ?>>

        <?php else: ?>
            <input class="form-control<?= $hasError ? ' is-invalid' : '' ?>" type="<?= e($type) ?>"
                   value="<?= $type === 'password' ? '' : e((string) $value) ?>" placeholder="<?= e($placeholder) ?>"<?= html_attrs($common) ?>>
        <?php endif; ?>

        <?php if ($toggle): ?>
            <button type="button" class="sc-field__toggle" data-toggle-password="#<?= e($id) ?>" aria-label="Show password"><i class="bi bi-eye" aria-hidden="true"></i></button>
        <?php endif; ?>
    </div>
<?php endif; ?>

    <?php if ($hasError): ?>
        <div class="sc-field__error" id="<?= e($id) ?>-error"><i class="bi bi-exclamation-circle" aria-hidden="true"></i> <?= e($error) ?></div>
    <?php elseif ($help !== ''): ?>
        <div class="sc-field__help" id="<?= e($id) ?>-help"><?= e($help) ?></div>
    <?php endif; ?>
</div>
