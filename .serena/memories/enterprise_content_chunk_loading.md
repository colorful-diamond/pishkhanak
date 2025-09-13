# Enterprise Content Generation - Chunk Loading Pattern

## 🚨 CRITICAL: File Loading Requirements

### MANDATORY Chunk Loading for Large Files
When loading any content file (especially reference files like credit-score-rating):

```python
# CORRECT APPROACH - Load in chunks
file_path = "/path/to/content.blade.php"
total_lines = get_file_line_count(file_path)
chunk_size = 500

for offset in range(0, total_lines, chunk_size):
    chunk = Read(file_path, limit=chunk_size, offset=offset)
    process_chunk(chunk)
```

### Implementation Pattern:
1. **First**: Check file size/line count
2. **Then**: Read in 500-line chunks
3. **Process**: Each chunk sequentially
4. **Combine**: Results after all chunks processed

### Example for credit-score-rating:
```bash
# Step 1: Read lines 1-500
Read("/path/to/credit-score-rating/content.blade.php", limit=500, offset=0)

# Step 2: Read lines 501-1000  
Read("/path/to/credit-score-rating/content.blade.php", limit=500, offset=500)

# Step 3: Read lines 1001-1500
Read("/path/to/credit-score-rating/content.blade.php", limit=500, offset=1000)

# Continue until complete...
```

### Token Management:
- Each chunk: Max 500 lines
- Process incrementally
- Extract patterns per chunk
- Combine patterns at end

### Benefits:
- Avoids token limit errors
- Enables processing large files
- Maintains context awareness
- Allows pattern extraction from entire file

## UPDATED WORKFLOW:
1. Check file exists
2. Get total line count
3. Calculate chunks needed
4. Read each 500-line chunk
5. Extract patterns from chunks
6. Combine all patterns
7. Generate content based on complete understanding

This approach ensures we can process files of ANY size without hitting token limits!