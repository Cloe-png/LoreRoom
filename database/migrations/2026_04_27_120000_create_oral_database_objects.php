<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP TRIGGER IF EXISTS trg_character_relations_before_insert');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_world_character_count');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_world_dashboard');

        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_character_relations_before_insert
BEFORE INSERT ON character_relations
FOR EACH ROW
BEGIN
    IF NEW.from_character_id = NEW.to_character_id THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Un personnage ne peut pas etre en relation avec lui-meme.';
    END IF;

    IF EXISTS (
        SELECT 1
        FROM character_relations
        WHERE from_character_id = NEW.from_character_id
          AND to_character_id = NEW.to_character_id
          AND relation_type = NEW.relation_type
    ) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Cette relation existe deja pour ce duo de personnages.';
    END IF;
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE FUNCTION fn_world_character_count(p_world_id BIGINT UNSIGNED)
RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_total INT DEFAULT 0;

    SELECT COUNT(*)
      INTO v_total
      FROM characters
     WHERE world_id = p_world_id;

    RETURN v_total;
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_world_dashboard(IN p_world_id BIGINT UNSIGNED)
BEGIN
    SELECT
        w.id,
        w.name,
        w.status,
        fn_world_character_count(w.id) AS character_count,
        (SELECT COUNT(*) FROM places WHERE world_id = w.id) AS place_count,
        (SELECT COUNT(*) FROM chronicles WHERE world_id = w.id) AS chronicle_count,
        (SELECT COUNT(*) FROM factions WHERE world_id = w.id) AS faction_count
    FROM worlds w
    WHERE w.id = p_world_id
    LIMIT 1;
END
SQL);
    }

    public function down()
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP TRIGGER IF EXISTS trg_character_relations_before_insert');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_world_character_count');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_world_dashboard');
    }
};
