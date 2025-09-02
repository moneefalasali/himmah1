## Todo List

### Phase 1: Extract and analyze existing Laravel project structure
- [x] Unzip the provided Laravel project file.
- [x] List contents of the Himmah directory.
- [x] List contents of the database directory to check for existing migrations.

### Phase 2: Create database migrations for universities and course mapping system
- [x] Create `universities` table migration.
- [x] Add `university_id` to `users` table migration.
- [x] Create `uni_courses` table migration.
- [x] Create `course_lesson_mappings` table migration.

### Phase 3: Implement Eloquent models with proper relationships
- [x] Create `University` model.
- [x] Update `User` model.
- [x] Update `Course` model.
- [x] Create `UniCourse` model.
- [x] Update `Lesson` model.

### Phase 4: Update user registration form and authentication system
- [x] Add university dropdown to registration form.
- [x] Save `university_id` during registration.

### Phase 5: Create admin panel CRUD interfaces for universities and course mappings
- [x] Implement CRUD for Universities.
- [x] Implement CRUD for Uni_Courses.
- [x] Implement CRUD for Lesson Mappings (drag-and-drop ordering if possible).

### Phase 6: Update frontend course display logic for university-specific content
- [x] Fetch `uni_courses` for logged-in user's `university_id`.
- [x] Display lessons in mapped order.

### Phase 7: Create database seeders for universities and course mappings
- [x] Seed universities (KSU, KAU, etc.).
- [x] Map existing courses to each university in `uni_courses`.
- [x] Map lessons for each `uni_course` using `course_lesson_mappings`.

### Phase 8: Update pricing system and course descriptions
- [x] Implement pricing logic for individual courses.
- [x] Highlight "Free Summary + Tajmeeat included" in course description.

### Phase 9: Create documentation and deliver final implementation
- [x] Create documentation for adding universities/courses/mappings.
- [x] Create installation guide.
- [x] Create changelog.
- [x] Deliver final implementation.

