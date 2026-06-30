<div class="form-grid">
    <div class="form-field">
        <label for="name">Full name</label>
        <input id="name" name="name" type="text" value="{{ old('name', $employee->name) }}" required>
        @error('name') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-field">
        <label for="employee_code">Employee code</label>
        <input id="employee_code" name="employee_code" type="text" value="{{ old('employee_code', $employee->employee_code) }}" required>
        @error('employee_code') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-field">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $employee->email) }}" required>
        @error('email') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-field">
        <label for="phone">Phone</label>
        <input id="phone" name="phone" type="text" value="{{ old('phone', $employee->phone) }}" required>
        @error('phone') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-field">
        <label for="status">Status</label>
        <select id="status" name="status" required>
            @foreach ([App\Models\User::STATUS_PENDING, App\Models\User::STATUS_APPROVED, App\Models\User::STATUS_REJECTED] as $status)
                <option value="{{ $status }}" @selected(old('status', $employee->status) === $status)>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>
        @error('status') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-field">
        <label for="branch_position">Branch position</label>
        <input id="branch_position" name="branch_position" type="text" value="{{ old('branch_position', $employee->branches->first()?->pivot?->position) }}">
        @error('branch_position') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-field">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="new-password" @if (! $employee->exists) required @endif>
        @error('password') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-field">
        <label for="password_confirmation">Confirm password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" @if (! $employee->exists) required @endif>
    </div>

    <fieldset class="checkbox-list">
        <legend>Assigned branches</legend>
        <div class="checkbox-grid">
            @forelse ($branches as $branch)
                <label class="checkbox-choice">
                    <input
                        name="branch_ids[]"
                        type="checkbox"
                        value="{{ $branch->id }}"
                        @checked(in_array($branch->id, old('branch_ids', $assignedBranchIds), false))
                    >
                    {{ $branch->name }} · {{ $branch->code }}
                </label>
            @empty
                <p>No active branches available yet.</p>
            @endforelse
        </div>
        @error('branch_ids') <p class="field-error">{{ $message }}</p> @enderror
    </fieldset>
</div>

<div class="form-actions">
    <button class="button" type="submit">{{ $submitLabel }}</button>
</div>
