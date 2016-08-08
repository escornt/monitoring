#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <sys/types.h>
#include <unistd.h>
#include <fcntl.h>
#include <sys/stat.h>
#include <dirent.h>

char              *concat(char *s1, char *s2)
{
  int             i;
  int             j;
  char            *tmp;
  char            *res;

  i = 0;
  j = 0;
  tmp = "cttime";
  if ((res = malloc(strlen(s1) + strlen(s2) + 10)) == NULL)
    return (NULL);
  while (i < strlen(s1))
    res[j++] = s1[i++];
  res[j++] = '/';
  i = 0;
  while (i < strlen(s2))
    res[j++] = s2[i++];
  i = 0;
  res[j++] = '/';
  while (i < 6)
    res[j++] = tmp[i++];
  res[j] = 0;
  return (res);
}

int               start_parsing(struct dirent *dit, char *path)
{
  FILE            *fd;
  char            *fullpath;
  char            *line;

  if ((fullpath = concat(path, dit->d_name)) == NULL)
    return (1);
  if ((fd = fopen(fullpath, "r")) == NULL)
    {
      free(fullpath);
      return (1);
    }
  getline(&line, 0, fd);
  handle_line(line, dit->d_name);
  free(fullpath);
  fclose(fd);
  return (0);
}

int               main()
{
  DIR             *dir;
  struct dirent   *dit;
  char            *path;

  path = "/mnt/pve/NFS/private/";
  if (dir = opendir(path) == NULL)
    return (1);
  while ((dit = readdir(dir)) != NULL)
  {
    printf("%s\n", dit->d_name);
  }
  //  start_parsing(dit, path);
  return (0);
}
