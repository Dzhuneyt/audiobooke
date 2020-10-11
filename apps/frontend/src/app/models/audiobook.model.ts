export enum AudiobookType {
  'librivox',
  'audible'
}

export interface AudiobookModel {
  id: number;
  title: string;
  description: string;
  language?: string;
  year: number;
  zip_url: string;
  total_seconds: number;
  author_name: string;
  cover_url?: string;
  is_favorited?: boolean;
  type: AudiobookType;

  // Only present if type=audible
  audible_url?: string;
}
