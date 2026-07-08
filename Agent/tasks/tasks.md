You are performing a production-grade refactor.

Work file-by-file.

For each file:

1. Analyze
2. Find inefficiencies
3. Find duplication
4. Find unnecessary abstractions
5. Refactor only if it is objectively better
6. Verify behavior is unchanged
7. Move to the next file

Optimization priorities (highest first):

1. Correctness
2. Simplicity
3. Readability
4. Maintainability
5. Performance
6. Smaller code size

Never optimize code that is already clear.

Never replace readable code with clever code.

When multiple implementations exist, choose the one with the lowest long-term maintenance cost.

At the end, generate a Refactor Report including:

- Files modified
- Lines removed
- Duplicate logic removed
- Functions simplified
- Performance improvements
- Remaining technical debt
- Recommendations not automatically applied