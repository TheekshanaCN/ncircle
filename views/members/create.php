<?php /** views/members/create.php  -  post a Find Members request */
$picked = old("faculties", []);
$picked = is_array($picked) ? $picked : [];
?>
<?= component("page-header", [
    "title" => "Post a request",
    "subtitle" => "Students from any faculty can see this and respond.",
    "actions" => component("button", ["label" => "Back", "icon" => "arrow-left", "variant" => "outline", "size" => "sm", "href" => url("/members"), "class" => "sc-btn-quiet"]),
]) ?>
<div class="sc-form-layout">
    <form class="sc-panel" method="post" action="<?= e(url("/members/requests")) ?>">
        <?= csrf_field() ?>
        <?= component("form-field", ["name" => "title", "label" => "What do you need?", "placeholder" => "Need a Business student for a market research project", "required" => true, "attrs" => ["maxlength" => 160]]) ?>
        <?= component("form-field", ["name" => "type", "label" => "Type", "type" => "select", "placeholder" => "Choose a type", "options" => config("app.help_types"), "required" => true]) ?>
        <?= component("form-field", ["name" => "description", "label" => "Details", "type" => "textarea", "rows" => 5, "placeholder" => "What is the project, what would they do, and how much time would it take?", "required" => true, "attrs" => ["maxlength" => 2000]]) ?>
        <?= component("form-field", ["name" => "skills", "label" => "Skills needed (optional)", "placeholder" => "Surveys, Statistics", "help" => "Separate skills with commas.", "attrs" => ["maxlength" => 255]]) ?>
        <fieldset class="sc-field">
            <legend class="form-label">Which faculties are you looking in?</legend>
            <div class="members-faculties">
                <?php foreach (config("app.faculties") as $value => $label): ?>
                    <label class="browse-check"><input class="form-check-input" type="checkbox" name="faculties[]" value="<?= e($value) ?>"<?= in_array($value, $picked, true) ? " checked" : "" ?>> <span><?= e($label) ?></span></label>
                <?php endforeach; ?>
            </div>
            <div class="sc-field__help">Leave these empty to welcome every faculty.</div>
        </fieldset>
        <?= component("button", ["label" => "Post request", "type" => "submit", "variant" => "dark", "icon" => "send"]) ?>
    </form>
    <aside class="sc-panel sc-panel--tight sc-side-note">
        <h2 class="sc-panel__title"><i class="bi bi-shield-lock" aria-hidden="true"></i> How contact works</h2>
        <ul>
            <li>People respond with a short message.</li>
            <li>You accept or decline each response.</li>
            <li>After an accept, you can both see each other's university email. Nobody else can.</li>
        </ul>
    </aside>
</div>
